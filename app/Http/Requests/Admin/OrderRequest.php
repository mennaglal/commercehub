<?php

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Only the basic shape is checked here.
 * Business rules (stock, coupon, variant belongs to product, price...) live in the services,
 * and the price is NEVER taken from the form.
 */
class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // access is already protected by the admin middleware on the routes
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'shipping_total' => ['nullable', 'numeric', 'min:0', 'max:99999999'],

            'coupon_code' => ['nullable', 'string', 'max:50'],

            'payment_method' => ['required', Rule::in(Order::PAYMENT_METHODS)],
            // The gateway is needed only for online payment (otherwise it is ignored).
            'payment_gateway' => [
                'nullable',
                'required_if:payment_method,' . Order::PAYMENT_ONLINE,
                Rule::in(array_keys(config('orders.gateways'))),
            ],

            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ];
    }
}
