<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $city = $this->route('city');

        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'name_ar')
                    ->where('country_id', $this->input('country_id'))
                    ->ignore($city?->id),
            ],
            'name_en' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
