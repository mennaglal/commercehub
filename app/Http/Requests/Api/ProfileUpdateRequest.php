<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Only these fields can be changed from the app.
 * (Before, $request->all() went straight to update(): a user could send "status" and change his own account status.)
 * The email is not here on purpose: changing it needs an email verification.
 */
class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],

            'country_id' => ['sometimes', 'nullable', 'integer', Rule::exists('countries', 'id')->where('is_active', true)],
            'city_id' => [
                'sometimes', 'nullable', 'integer',
                Rule::exists('cities', 'id')->where('is_active', true)->where('country_id', $this->input('country_id')),
            ],
            'area_id' => [
                'sometimes', 'nullable', 'integer',
                Rule::exists('areas', 'id')->where('is_active', true)->where('city_id', $this->input('city_id')),
            ],
        ];
    }
}
