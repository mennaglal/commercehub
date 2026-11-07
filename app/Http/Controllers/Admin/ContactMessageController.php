<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class ContactMessageController extends Controller  implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:contact-messages.view', only: ['index','show']),
            new Middleware('permission:contact-messages.update', only: ['edit', 'update']),
        ];
    }

    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('status') &&
            in_array($request->status, ContactMessage::statuses(), true)) {

            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $messages = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $newMessagesCount = ContactMessage::where(
            'status',
            ContactMessage::STATUS_NEW
        )->count();

        return view(
            'admin.pages.contact-messages.index',
            compact('messages', 'newMessagesCount')
        );
    }

    public function show($id)
    {
        $contactMessage = ContactMessage::findOrFail($id);

        if ($contactMessage->status === ContactMessage::STATUS_NEW) {
            $contactMessage->update([
                'status' => ContactMessage::STATUS_READ,
                'read_at' => now(),
            ]);
        }

        return view(
            'admin.pages.contact-messages.show',
            compact('contactMessage')
        );
    }

    public function update(Request $request, $id)
    {
        $contactMessage = ContactMessage::findOrFail($id);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(ContactMessage::statuses()),
            ],
        ]);

        $status = $validated['status'];

        $data = [
            'status' => $status,
        ];

        if ($status !== ContactMessage::STATUS_NEW &&
            !$contactMessage->read_at) {

            $data['read_at'] = now();
        }

        if ($status === ContactMessage::STATUS_REPLIED) {
            $data['replied_at'] = $contactMessage->replied_at ?? now();
        }

        $contactMessage->update($data);

        return redirect()
            ->route('admin.contact-messages.show', $contactMessage->id)
            ->with('success', __('contact-messages.updated_successfully'));
    }
}
