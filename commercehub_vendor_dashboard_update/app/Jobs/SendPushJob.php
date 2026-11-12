<?php

namespace App\Jobs;

use App\Services\PushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Sends ONE push notification to up to 500 users, in the background.
 * Firebase can be slow or down for a moment, so the job is tried 3 times (after 30 seconds, then 2 minutes).
 */
class SendPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public array $backoff = [30, 120];

    public function __construct(public array $userIds, public array $payload)
    {
    }

    public function handle(PushService $push): void
    {
        $push->sendToUsers($this->userIds, $this->payload);
    }
}
