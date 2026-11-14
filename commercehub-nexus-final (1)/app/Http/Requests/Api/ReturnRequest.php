<?php

namespace App\Http\Requests\Api;

use App\Models\OrderReturn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * POST /orders/{orderNumber}/returns
 * The rules (delivered, return period, remaining quantity) and the refund amount are checked / calculated
 * in ReturnService. Here only the shape of the request.
 */
class ReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', Rule::in(OrderReturn::REASONS)],
            'customer_note' => ['nullable', 'string', 'max:1000'],

            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.order_item_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ];
    }
}
