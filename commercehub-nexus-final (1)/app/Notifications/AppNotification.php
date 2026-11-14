<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * ONE notification class for the whole system. The content is a simple array:
 *   type (order, return, store, contact, broadcast, system), title_ar, title_en, body_ar, body_en, url
 * To add a new kind of notification you only add a text in lang/xx/notifications.php and a call to
 * NotificationService, you do not need a new class.
 *
 * It is queued: with QUEUE_CONNECTION=sync it is sent immediately, otherwise run "php artisan queue:work".
 */
class AppNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $payload)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload;
    }

    // title / body in the current language (falls back to English).
    public static function text(array $data, string $field, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return (string) (($data[$field . '_' . $locale] ?? null) ?: ($data[$field . '_en'] ?? ''));
    }
}
