<?php

namespace App\Http\Requests\Admin;

use App\Models\OrderReturn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Only the basic shape is checked here. The real rules (delivered, return window,
 * remaining quantity, amounts) are checked in ReturnService, and the refund is calculated there.
 */
class ReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // protected by the permission middleware on the routes
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', Rule::in(OrderReturn::REASONS)],
            'customer_note' => ['nullable', 'string', 'max:1000'],

            // items[order_item_id] = quantity (0 or empty = not returned)
            'items' => ['required', 'array', 'max:100'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:10000'],
        ];
    }
}
