<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Everything about checking a coupon and calculating:
 *   - the customer discount
 *   - the blogger commission
 * It must be called INSIDE the order transaction (the coupon row is locked).
 */
class CouponService
{
    /**
     * @param array $lines the order lines built by OrderService (each has 'product' and 'line_total')
     * @return array{coupon: Coupon, discount: float, commission: float, eligible_keys: int[]}
     */
    public function apply(string $code, array $lines): array
    {
        // Locked, so two orders cannot use the last allowed use at the same time.
        $coupon = Coupon::with('blogger')
            ->where('code', Str::upper(trim($code)))
            ->lockForUpdate()
            ->first();

        // A coupon of a deactivated blogger is not valid.
        if (! $coupon || ! $coupon->status || ($coupon->blogger_id && ! $coupon->blogger?->status)) {
            $this->fail(__('coupons.errors.invalid'));
        }

        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            $this->fail(__('coupons.errors.not_started'));
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            $this->fail(__('coupons.errors.expired'));
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            $this->fail(__('coupons.errors.limit_reached'));
        }

        // Only the products of the coupon categories take part in the discount.
        $categoryIds = $this->categoryIds($coupon);

        $eligibleLines = collect($lines)
            ->filter(fn ($line) => in_array((int) $line['product']->category_id, $categoryIds, true));

        $eligible = round($eligibleLines->sum('line_total'), 2);

        if ($eligible <= 0) {
            $this->fail(__('coupons.errors.no_eligible_items'));
        }

        if ($coupon->min_order_amount !== null && $eligible < (float) $coupon->min_order_amount) {
            $this->fail(__('coupons.errors.min_amount', ['amount' => number_format($coupon->min_order_amount, 2)]));
        }

        // Customer discount (never more than the eligible products cost).
        $discount = $coupon->type === Coupon::PERCENTAGE
            ? $eligible * (float) $coupon->value / 100
            : (float) $coupon->value;

        if ($coupon->type === Coupon::PERCENTAGE && $coupon->max_discount !== null) {
            $discount = min($discount, (float) $coupon->max_discount);
        }

        $discount = round(min($discount, $eligible), 2);

        // Blogger commission: calculated on what the customer really pays for the eligible products.
        $commission = 0.0;

        if ($coupon->blogger_id) {
            $base = $eligible - $discount;

            $commission = $coupon->commission_type === Coupon::PERCENTAGE
                ? $base * (float) $coupon->commission_value / 100
                : (float) $coupon->commission_value;

            $commission = round(min($commission, $base), 2);
        }

        // "eligible_keys" = which lines the coupon applies to (OrderService splits the discount and
        // the commission between them, so every order item knows its own share).
        return [
            'coupon' => $coupon,
            'discount' => $discount,
            'commission' => $commission,
            'eligible_keys' => $eligibleLines->keys()->all(),
        ];
    }

    /**
     * The coupon categories + their subcategories.
     * (A coupon on "Clothes" also works on the products of "Men shirts".)
     *
     * @return int[]
     */
    private function categoryIds(Coupon $coupon): array
    {
        $ids = $coupon->categories()->pluck('categories.id')->map(fn ($id) => (int) $id);

        $childIds = Category::whereIn('parent_id', $ids)->pluck('id')->map(fn ($id) => (int) $id);

        return $ids->merge($childIds)->unique()->values()->all();
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['coupon_code' => $message]);
    }
}
