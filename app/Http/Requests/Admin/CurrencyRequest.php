<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
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
        $currency = $this->route('currency');

        $codeRules = [
            'required',
            'string',
            'size:3',
            'alpha',
            Rule::unique('currencies', 'code')->ignore($currency?->id),
        ];

        // The base currency code can never be changed.
        if ($currency?->code === 'EGP') {
            $codeRules[] = Rule::in(['EGP']);
        }

        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'code' => $codeRules,
            'symbol' => ['required', 'string', 'max:10'],
            'decimal_places' => ['required', 'integer', 'min:0', 'max:9'],
            // Every currency except the base one needs an exchange rate.
            'rate_to_egp' => [
                Rule::requiredIf(fn () => $this->input('code') !== 'EGP'),
                'nullable',
                'numeric',
                'gt:0',
            ],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'rate_to_egp.required' => 'يجب إدخال سعر صرف صحيح للعملة.',
            'code.in' => 'لا يمكن تغيير كود العملة الأساسية EGP.',
        ];
    }
}
