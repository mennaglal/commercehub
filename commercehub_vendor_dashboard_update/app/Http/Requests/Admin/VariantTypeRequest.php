<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VariantTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'input_type' => ['required', Rule::in(['text', 'color'])],
            'status' => ['required', 'boolean'],
        ];
    }
}
