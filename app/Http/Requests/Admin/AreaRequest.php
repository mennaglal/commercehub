<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $area = $this->route('area');

        return [
            'name_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('areas', 'name_ar')
                    ->where('city_id', $this->input('city_id'))
                    ->ignore($area?->id),
            ],
            'name_en' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
