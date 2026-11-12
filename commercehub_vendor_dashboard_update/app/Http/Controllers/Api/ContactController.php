<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactRequest;
use App\Http\Responses\ApiResponse;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    // POST /contact (the route is throttled: 5 messages per minute per IP)
    public function store(ContactRequest $request)
    {
        ContactMessage::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'subject' => $request->validated('subject'),
            'message' => $request->validated('message'),
            'status' => ContactMessage::STATUS_NEW,
        ]);

        return ApiResponse::success(null, 'Your message was sent. Thank you!', 201);
    }
}
