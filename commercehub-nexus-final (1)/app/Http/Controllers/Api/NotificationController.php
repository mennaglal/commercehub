<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\AppNotification;
use Illuminate\Http\Request;

// The notifications of the logged-in user (customer or vendor). Routes need auth:sanctum.
// Every query goes through $request->user(), so nobody can read or change another user's notifications.
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()->notifications()->paginate(20);

        return response()->json([
            'data' => $items->getCollection()->map(fn ($notification) => [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? 'system',
                'title' => AppNotification::text($notification->data, 'title'),
                'body' => AppNotification::text($notification->data, 'body'),
                'url' => $notification->data['url'] ?? null,
                'read' => $notification->read_at !== null,
                'created_at' => $notification->created_at->toIso8601String(),
            ]),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function unreadCount(Request $request)
    {
        return response()->json(['count' => $request->user()->unreadNotifications()->count()]);
    }

    public function markRead(Request $request, string $id)
    {
        $request->user()->notifications()->whereKey($id)->firstOrFail()->markAsRead();

        return response()->json(['message' => 'ok']);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['message' => 'ok']);
    }
}
