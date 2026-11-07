<?php

namespace App\Jobs;

use App\Models\NotificationBroadcast;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * The message an admin wrote is sent to thousands of users in the background,
 * so the admin screen answers immediately.
 * Tried only once: a second try would send the in-app notification twice to the users already done.
 */
class SendBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 1;

    public function __construct(public int $broadcastId)
    {
    }

    public function handle(NotificationService $notifications): void
    {
        $notifications->deliverBroadcast(NotificationBroadcast::findOrFail($this->broadcastId));
    }
}
