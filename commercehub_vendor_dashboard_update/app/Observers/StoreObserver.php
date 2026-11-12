<?php

namespace App\Observers;

use App\Models\Store;
use App\Services\NotificationService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

/**
 * ASSUMPTION: stores.approval_status uses the values "pending", "approved", "rejected".
 * If your values are different, change them here.
 */
class StoreObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(private NotificationService $notifications)
    {
    }

    // A new store waiting for approval: tell the admins who approve vendors.
    public function created(Store $store): void
    {
        if ($store->approval_status !== 'pending') {
            return;
        }

        $this->notifications->toAdmins(
            'vendors.approve',
            $this->notifications->payload('store', 'new_store', ['store' => $store->name], route('admin.vendors.index'))
        );
    }

    // The admin decided: tell the store owner.
    public function updated(Store $store): void
    {
        if (! $store->wasChanged('approval_status') || ! in_array($store->approval_status, ['approved', 'rejected'], true)) {
            return;
        }

        $owner = $store->user;

        if ($owner) {
            $this->notifications->toUsers(
                collect([$owner]),
                $this->notifications->payload('store', 'store_' . $store->approval_status, ['store' => $store->name])
            );
        }
    }
}
