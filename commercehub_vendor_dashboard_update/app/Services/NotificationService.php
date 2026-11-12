<?php

namespace App\Services;

use App\Jobs\SendBroadcastJob;
use App\Jobs\SendPushJob;
use App\Models\NotificationBroadcast;
use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * The ONE place that sends notifications. Every notification goes to two places:
 *   1) the database  -> the in-app inbox (and the bell of the admin panel)
 *   2) Firebase push -> the phone (only when FCM_ENABLED=true)
 */
class NotificationService
{
    /**
     * Builds the content of a notification from lang/xx/notifications.php => events.<event>.
     * Both languages are saved, so every user sees it in his own language.
     */
    public function payload(string $type, string $event, array $replace = [], ?string $url = null): array
    {
        return [
            'type' => $type,
            'title_ar' => __("notifications.events.$event.title", $replace, 'ar'),
            'title_en' => __("notifications.events.$event.title", $replace, 'en'),
            'body_ar' => __("notifications.events.$event.body", $replace, 'ar'),
            'body_en' => __("notifications.events.$event.body", $replace, 'en'),
            'url' => $url,
        ];
    }

    // Notify every ACTIVE admin that has the permission (example: "orders.view").
    public function toAdmins(string $permission, array $payload): void
    {
        $admins = User::permission($permission)->where('status', true)->get();

        $this->toUsers($admins, $payload);
    }

    public function toUsers(Collection $users, array $payload): void
    {
        if ($users->isEmpty()) {
            return;
        }

        // 1) the in-app inbox (queued)
        Notification::send($users, new AppNotification($payload));

        // 2) the phones
        $this->push($users->pluck('id')->all(), $payload);
    }

    // Push to the phones of these users (in the background, 500 users per job).
    public function push(array $userIds, array $payload): void
    {
        if (! config('push.enabled') || empty($userIds)) {
            return;
        }

        foreach (array_chunk($userIds, 500) as $ids) {
            SendPushJob::dispatch($ids, $payload);
        }
    }

    // A readable status for the texts: "Shipped" / "تم الشحن" (falls back to a headline of the code).
    public function statusLabel(string $group, string $value, string $locale): string
    {
        $key = "orders.$group.$value";

        return Lang::has($key, $locale) ? __($key, [], $locale) : Str::headline($value);
    }

    /* ---------------- Messages written by an admin ---------------- */

    /**
     * Step 1 (inside the web request): save the message and queue the sending. It answers immediately.
     */
    public function broadcast(array $data, ?int $adminId): NotificationBroadcast
    {
        $broadcast = NotificationBroadcast::create([
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'body_ar' => $data['body_ar'],
            'body_en' => $data['body_en'],
            'url' => $data['url'] ?? null,
            'audience' => $data['audience'],
            'user_id' => $data['audience'] === 'user' ? User::where('email', $data['user_email'])->value('id') : null,
            'with_push' => ! empty($data['push']),
            'sent_by' => $adminId,
        ]);

        SendBroadcastJob::dispatch($broadcast->id);

        return $broadcast;
    }

    /**
     * Step 2 (in the queue, see SendBroadcastJob): send to the audience.
     * The users are read in chunks of 500, so a big audience does not fill the memory.
     */
    public function deliverBroadcast(NotificationBroadcast $broadcast): void
    {
        $payload = [
            'type' => 'broadcast',
            'title_ar' => $broadcast->title_ar,
            'title_en' => $broadcast->title_en,
            'body_ar' => $broadcast->body_ar,
            'body_en' => $broadcast->body_en,
            'url' => $broadcast->url,
        ];

        $notification = new AppNotification($payload);

        $users = match ($broadcast->audience) {
            'customers' => User::role('customer'),
            'vendors' => User::role('vendor'),
            'all' => User::role(['customer', 'vendor']),
            'user' => User::whereKey($broadcast->user_id),
        };

        $count = 0;

        $users->where('status', true)->chunkById(500, function ($chunk) use ($notification, $payload, $broadcast, &$count) {
            // sendNow: we are already in the queue, so no extra job per user.
            Notification::sendNow($chunk, $notification);

            if ($broadcast->with_push) {
                $this->push($chunk->pluck('id')->all(), $payload);
            }

            $count += $chunk->count();
        });

        $broadcast->update(['recipients_count' => $count]);
    }
}
