<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BecomeVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }
}
