<?php

namespace App\Console\Commands;

use App\Models\DeviceToken;
use App\Models\User;
use App\Services\PushService;
use Illuminate\Console\Command;

// php artisan push:test user@example.com
// Sends a test push RIGHT NOW (not queued) to the phones of the user, to check the Firebase setup.
class PushTestCommand extends Command
{
    protected $signature = 'push:test {email : the email of the user} {--title=Test} {--body=Push notifications are working}';

    protected $description = 'Send a test push notification to the devices of a user';

    public function handle(PushService $push): int
    {
        if (! config('push.enabled')) {
            $this->warn('FCM_ENABLED is false in .env. The test is sent anyway.');
        }

        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('User not found.');

            return self::FAILURE;
        }

        $devices = DeviceToken::where('user_id', $user->id)->count();
        $this->info("The user has {$devices} registered device(s).");

        if ($devices === 0) {
            $this->warn('No device: the app must call POST /api/device-tokens first.');

            return self::FAILURE;
        }

        $title = (string) $this->option('title');
        $body = (string) $this->option('body');

        try {
            $accepted = $push->sendToUsers([$user->id], [
                'type' => 'system',
                'title_ar' => $title, 'title_en' => $title,
                'body_ar' => $body, 'body_en' => $body,
                'url' => null,
            ]);
        } catch (\Throwable $e) {
            $this->error('Firebase error: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info("Firebase accepted the message for {$accepted} device(s).");

        return self::SUCCESS;
    }
}
