<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BroadcastRequest;
use App\Models\NotificationBroadcast;
use App\Notifications\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

/**
 * Two things in one controller:
 *  - the inbox of the logged-in admin (index, unread, open, readAll, destroy)  -> notifications.view
 *  - sending a message to customers / vendors (compose, send)                  -> notifications.send
 * An admin only ever reads and deletes HIS OWN notifications (always queried through $request->user()).
 */
class NotificationController extends Controller implements HasMiddleware
{
    public function __construct(private NotificationService $notifications)
    {
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:notifications.view', only: ['index', 'unread', 'open', 'readAll', 'destroy']),
            new Middleware('permission:notifications.send', only: ['compose', 'send']),
        ];
    }

    public function index(Request $request)
    {
        $unreadOnly = $request->query('filter') === 'unread';

        $user = $request->user();

        $notifications = ($unreadOnly ? $user->unreadNotifications() : $user->notifications())
            ->paginate(20)
            ->withQueryString();

        return view('admin.pages.notifications.index', [
            'notifications' => $notifications,
            'unreadOnly' => $unreadOnly,
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    // JSON for the bell in the top bar (count + the latest 8).
    public function unread(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'items' => $user->notifications()->limit(8)->get()->map(fn ($notification) => [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? 'system',
                'title' => AppNotification::text($notification->data, 'title'),
                'body' => AppNotification::text($notification->data, 'body'),
                'read' => $notification->read_at !== null,
                'time' => $notification->created_at->diffForHumans(),
                'url' => route('admin.notifications.open', $notification->id),
            ]),
        ]);
    }

    // Marks it as read and goes to its page.
    public function open(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->whereKey($id)->firstOrFail();

        $notification->markAsRead();

        return redirect($this->safeUrl($notification->data['url'] ?? null) ?? route('admin.notifications.index'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', __('notifications.messages.all_read'));
    }

    public function destroy(Request $request, string $id)
    {
        $request->user()->notifications()->whereKey($id)->delete();

        return back()->with('success', __('notifications.messages.deleted'));
    }

    public function compose()
    {
        return view('admin.pages.notifications.compose', [
            'audiences' => NotificationBroadcast::AUDIENCES,
            'history' => NotificationBroadcast::latest()->limit(15)->get(),
            'pushEnabled' => (bool) config('push.enabled'),
        ]);
    }

    public function send(BroadcastRequest $request)
    {
        // The message is saved and the sending is queued: the screen answers immediately,
        // even when the audience is thousands of users.
        $this->notifications->broadcast($request->validated(), $request->user()->id);

        return redirect()
            ->route('admin.notifications.compose')
            ->with('success', __('notifications.messages.queued'));
    }

    // Redirect only inside this website (never to an outside link: no "open redirect").
    private function safeUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (Str::startsWith($url, url('/')) || (Str::startsWith($url, '/') && ! Str::startsWith($url, '//'))) {
            return $url;
        }

        return null;
    }
}
