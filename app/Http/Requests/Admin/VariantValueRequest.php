<?php

namespace App\Http\Requests\Admin;

use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VariantValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $variantType = $this->route('variantType');
        $variantValue = $this->route('variantValue');

        if (!$variantType instanceof VariantType) {
            $variantType = VariantType::findOrFail($variantType);
        }

        if ($variantValue && !$variantValue instanceof VariantValue) {
            $variantValue = VariantValue::findOrFail($variantValue);
        }

        $rules = [
            'value_ar' => ['required', 'string', 'max:255'],
            'value_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('variant_values', 'value_en')
                    ->where('variant_type_id', $variantType->id)
                    ->ignore($variantValue?->id),
            ],
            'status' => ['required', 'boolean'],
        ];

        $rules['color_code'] = $variantType->input_type === 'color'
            ? ['required', 'regex:/^#[0-9A-Fa-f]{6}$/']
            : ['nullable', 'prohibited'];

        return $rules;
    }
}
