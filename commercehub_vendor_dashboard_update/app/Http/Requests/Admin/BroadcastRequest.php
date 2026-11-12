<?php

namespace App\Http\Requests\Admin;

use App\Models\NotificationBroadcast;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BroadcastRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // protected by the permission middleware (notifications.send)
    }

    public function rules(): array
    {
        return [
            'audience' => ['required', Rule::in(NotificationBroadcast::AUDIENCES)],
            'user_email' => ['nullable', 'required_if:audience,user', 'email', 'exists:users,email'],
            'push' => ['nullable', 'boolean'],   // also send to the phones (Firebase)

            'title_ar' => ['required', 'string', 'max:120'],
            'title_en' => ['required', 'string', 'max:120'],
            'body_ar' => ['required', 'string', 'max:500'],
            'body_en' => ['required', 'string', 'max:500'],

            // Only web links (or empty), never "javascript:".
            'url' => ['nullable', 'url', 'starts_with:https://,http://', 'max:500'],
        ];
    }
}
