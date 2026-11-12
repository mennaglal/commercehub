<?php

namespace App\Observers;

use App\Models\OrderReturn;
use App\Services\NotificationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class OrderReturnObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(private NotificationService $notifications)
    {
    }

    // A new return request: tell the admins who handle returns.
    public function created(OrderReturn $return): void
    {
        $this->notifications->toAdmins(
            'returns.view',
            $this->notifications->payload('return', 'new_return', [
                'number' => $return->return_number,
                'order' => $return->order?->order_number,
            ], route('admin.returns.show', $return))
        );
    }
}
