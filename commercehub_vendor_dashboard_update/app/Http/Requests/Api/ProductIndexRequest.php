<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// GET /products?q=&category_id=&store_id=&min_price=&max_price=&on_sale=&in_stock=&sort=&per_page=
class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'store_id' => ['nullable', 'integer'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'on_sale' => ['nullable', 'boolean'],
            'in_stock' => ['nullable', 'boolean'],
            'sort' => ['nullable', Rule::in(['newest', 'price_asc', 'price_desc', 'name'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}
