<?php

namespace App\Http\Requests\Admin;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

// Used by create and edit.
class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Codes are always saved in capital letters, so "sale10" and "SALE10" are the same coupon.
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => Str::upper(trim((string) $this->input('code')))]);
        }
    }

    public function rules(): array
    {
        $hasBlogger = $this->filled('blogger_id');

        return [
            // $this->route('coupon') is null on create, and the coupon itself on edit.
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('coupons', 'code')->ignore($this->route('coupon'))],

            'type' => ['required', Rule::in(Coupon::TYPES)],
            'value' => ['required', 'numeric', 'gt:0', $this->input('type') === Coupon::PERCENTAGE ? 'max:100' : 'max:99999999'],
            'max_discount' => ['nullable', 'numeric', 'gt:0', 'max:99999999'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],

            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['required', 'boolean'],

            // At least one category: a coupon works only on the products of its categories.
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],

            // Empty blogger = general coupon. With a blogger, the commission is required.
            'blogger_id' => ['nullable', 'integer', 'exists:bloggers,id'],
            'commission_type' => [Rule::requiredIf($hasBlogger), 'nullable', Rule::in(Coupon::TYPES)],
            'commission_value' => [
                Rule::requiredIf($hasBlogger), 'nullable', 'numeric', 'min:0',
                $this->input('commission_type') === Coupon::PERCENTAGE ? 'max:100' : 'max:99999999',
            ],
        ];
    }
}
