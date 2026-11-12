<?php

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
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

            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],

            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('country_id', $this->input('country_id')),
            ],

            'area_id' => [
                'required',
                'integer',
                Rule::exists('areas', 'id')
                    ->where('city_id', $this->input('city_id')),
            ],

            'shipping_address' => [
                'required',
                'string',
                'max:1000',
            ],

            'shipping_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            // Shipping total must be calculated by the server from the selected area.
            'shipping_total' => [
                'prohibited',
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
