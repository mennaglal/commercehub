<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreating = $this->isMethod('POST');
        $user = $this->route('user');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($isCreating ? null : $user?->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'password' => [
                $isCreating ? 'required' : 'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'country_id' => [
                'nullable',
                'integer',
                'exists:countries,id',
            ],

            'city_id' => [
                'nullable',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('country_id', $this->input('country_id')),
            ],

            'area_id' => [
                'nullable',
                'integer',
                Rule::exists('areas', 'id')
                    ->where('city_id', $this->input('city_id')),
            ],
        ];
    }
}
