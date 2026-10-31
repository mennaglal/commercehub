<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('code')) {
            $this->merge(['code' => strtoupper(trim($this->input('code')))]);
        }
    }

    public function rules(): array
    {
        $country = $this->route('country');

        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('countries', 'name_ar')
                    ->where('currency_id', $this->input('currency_id'))
                    ->ignore($country?->id),
            ],
            'name_en' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'size:2',
                'alpha',
                Rule::unique('countries', 'code')->ignore($country?->id),
            ],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
