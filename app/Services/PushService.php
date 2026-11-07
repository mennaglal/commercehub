<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Notifications\AppNotification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Kreait\Laravel\Firebase\Facades\Firebase;

/**
 * Sends the push notifications through Firebase Cloud Messaging (package: kreait/laravel-firebase).
 * It is called only from the queue (SendPushJob) or from the "push:test" command, never from a web request.
 */
class PushService
{
    // Firebase accepts at most 500 tokens in one call.
    private const BATCH = 500;

    /**
     * @param  int[]  $userIds
     * @param  array  $payload  the same array saved in the notification (type, title_ar, title_en, body_ar, body_en, url)
     * @return int  how many phones accepted the message
     */
    public function sendToUsers(array $userIds, array $payload): int
    {
        $tokens = DeviceToken::whereIn('user_id', $userIds)->get(['token', 'locale']);

        if ($tokens->isEmpty()) {
            return 0;
        }

        $messaging = Firebase::messaging();
        $accepted = 0;

        // One message per language (the text is different), and at most 500 phones per call.
        foreach ($tokens->groupBy(fn ($row) => $row->locale === 'en' ? 'en' : 'ar') as $locale => $rows) {
            $message = CloudMessage::new()
                ->withNotification(FcmNotification::create(
                    AppNotification::text($payload, 'title', $locale),
                    AppNotification::text($payload, 'body', $locale)
                ))
                ->withData($this->data($payload))
                ->withAndroidConfig(AndroidConfig::fromArray(['priority' => 'high']));

            foreach ($rows->pluck('token')->chunk(self::BATCH) as $chunk) {
                $report = $messaging->sendMulticast($message, $chunk->values()->all());

                $this->deleteDeadTokens($report);
                $accepted += $report->successes()->count();
            }
        }

        return $accepted;
    }

    // The extra values the app reads when the user taps the notification. FCM accepts only strings here.
    private function data(array $payload): array
    {
        return array_filter([
            'type' => (string) ($payload['type'] ?? 'system'),
            'url' => (string) ($payload['url'] ?? ''),
        ], fn ($value) => $value !== '');
    }

    // The app was uninstalled or the token expired: Firebase says so, and we stop sending to it.
    private function deleteDeadTokens(MulticastSendReport $report): void
    {
        $dead = array_merge($report->unknownTokens(), $report->invalidTokens());

        if ($dead) {
            DeviceToken::whereIn('token', $dead)->delete();
        }
    }
}
