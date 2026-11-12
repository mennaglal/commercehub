<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

// Used by create and edit.
class BloggerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'boolean'],
        ];
    }
}
