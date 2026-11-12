<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // The category being updated (null while creating). SubCategoryRequest overrides it.
    protected function currentCategory()
    {
        return $this->route('category');
    }

    // Build the slug BEFORE validation, so the unique rule also checks a generated slug.
    protected function prepareForValidation(): void
    {
        $source = filled($this->input('slug'))
            ? $this->input('slug')
            : $this->input('name');

        $this->merge([
            'slug' => Str::slug((string) $source),
        ]);
    }

    public function rules(): array
    {
        $category = $this->currentCategory();

        return [
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($category?->id),
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.required' => __('dashboard.Please enter a valid English slug.'),
            'slug.unique' => __('dashboard.This slug already exists.'),
        ];
    }
}
