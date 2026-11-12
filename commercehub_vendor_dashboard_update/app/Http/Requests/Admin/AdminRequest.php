<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreating = $this->isMethod('POST');
        $admin = $this->route('admin');

        $roles = Role::whereNotIn('name', ['vendor', 'customer'])
            ->when(
                ! auth()->user()->hasRole('super-admin'),
                fn ($query) => $query->where('name', '!=', 'super-admin')
            )
            ->pluck('name')
            ->all();

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($isCreating ? null : $admin?->id),
            ],

            'password' => [
                $isCreating ? 'required' : 'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => ['required', Rule::in($roles)],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }
}
