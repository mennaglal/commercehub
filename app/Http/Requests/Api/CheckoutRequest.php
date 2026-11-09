<?php

namespace App\Http\Requests\Api;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * POST /orders            the full order.
 * POST /checkout/preview  only items (+ area_id and coupon_code): the answer is the cart summary.
 *
 * The prices, the discount, the shipping and the total are NEVER taken from the app: they are all
 * calculated by the server (OrderService), so nobody can change a price by editing the request.
 */
class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPreview = $this->routeIs('checkout.preview') || $this->is('api/checkout/preview');

        // Online gateways that are switched on in config/orders.php.
        $enabledGateways = collect(config('orders.gateways', []))
            ->filter(fn (array $gateway) => $gateway['enabled'] ?? false)
            ->keys()
            ->all();

        $required = $isPreview ? 'nullable' : 'required';

        return [
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],

            'coupon_code' => ['nullable', 'string', 'max:50'],

            // Shipping data: only the active areas, and the area must belong to the city, the city to the country.
            'country_id' => [$required, 'integer', Rule::exists('countries', 'id')->where('is_active', true)],
            'city_id' => [
                $required, 'integer',
                Rule::exists('cities', 'id')->where('is_active', true)->where('country_id', $this->input('country_id')),
            ],
            'area_id' => [
                $required, 'integer',
                Rule::exists('areas', 'id')->where('is_active', true)->where('city_id', $this->input('city_id')),
            ],

            // The rest is needed only to place the order.
            'customer_name' => [Rule::requiredIf(! $isPreview), 'nullable', 'string', 'max:255'],
            'customer_phone' => [Rule::requiredIf(! $isPreview), 'nullable', 'string', 'max:30'],
            'shipping_address' => [Rule::requiredIf(! $isPreview), 'nullable', 'string', 'max:1000'],
            'shipping_notes' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],

            'payment_method' => [Rule::requiredIf(! $isPreview), 'nullable', Rule::in(Order::PAYMENT_METHODS)],
            'payment_gateway' => [
                'exclude_unless:payment_method,' . Order::PAYMENT_ONLINE,
                'required', 'string', Rule::in($enabledGateways),
            ],

            // These can never come from the app.
            'shipping_total' => ['prohibited'],
            'user_id' => ['prohibited'],
        ];
    }
}
