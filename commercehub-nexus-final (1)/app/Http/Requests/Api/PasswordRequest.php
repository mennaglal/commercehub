<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],   // must match the real password
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ];
    }
}
