<?php

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Admin access must be enforced by route middleware or a policy.
        return true;
    }

    public function rules(): array
    {
        $enabledGateways = collect(config('orders.gateways', []))
            ->filter(fn (array $gateway) => $gateway['enabled'] ?? false)
            ->keys()
            ->all();

        return [
            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'customer_phone' => [
                'required',
                'string',
                'max:30',
            ],

            'shipping_country' => ['nullable', 'string', 'max:150'],
            'shipping_city' => ['nullable', 'string', 'max:150'],
            'shipping_area' => ['nullable', 'string', 'max:150'],

            'shipping_address' => [
                'required',
                'string',
                'max:1000',
            ],

            'shipping_notes' => ['nullable', 'string', 'max:1000'],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'shipping_total' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999',
            ],

            'coupon_code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'payment_method' => [
                'required',
                Rule::in(Order::PAYMENT_METHODS),
            ],

            'payment_gateway' => [
                'exclude_unless:payment_method,' . Order::PAYMENT_ONLINE,
                'required',
                'string',
                Rule::in($enabledGateways),
            ],

            'items' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.variant_id' => [
                'nullable',
                'integer',
                'exists:product_variants,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:10000',
            ],
        ];
    }
}
