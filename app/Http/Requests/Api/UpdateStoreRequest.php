<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// PUT /vendor/store (the vendor edits his store). Send as multipart/form-data when a logo or a banner is uploaded.
class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'country_id' => ['nullable', 'integer', Rule::exists('countries', 'id')->where('is_active', true)],
            'city_id' => [
                'nullable', 'integer',
                Rule::exists('cities', 'id')->where('is_active', true)->where('country_id', $this->input('country_id')),
            ],
            'area_id' => [
                'nullable', 'integer',
                Rule::exists('areas', 'id')->where('is_active', true)->where('city_id', $this->input('city_id')),
            ],
        ];
    }
}
