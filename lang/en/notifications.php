<?php

return [
    'title' => 'Notifications',
    'all' => 'All',
    'unread' => 'Unread',
    'mark_all_read' => 'Mark all as read',
    'open' => 'Open',
    'delete' => 'Delete',
    'empty' => 'No notifications.',
    'bell_empty' => 'You have no notifications.',
    'view_all' => 'View all',
    'send_title' => 'Send a notification',
    'history' => 'Sent messages',
    'history_empty' => 'No messages sent yet.',
    'send' => 'Send',
    'yes' => 'Yes',
    'no' => 'No',
    'push_disabled' => 'Push to phones is not enabled yet (FCM_ENABLED=false). The message will only appear in the in-app inbox.',
    'send_confirm' => 'Send this message now?',

    'fields' => [
        'audience' => 'Send to',
        'user_email' => 'User email',
        'title_ar' => 'Title (Arabic)',
        'title_en' => 'Title (English)',
        'body_ar' => 'Message (Arabic)',
        'body_en' => 'Message (English)',
        'url' => 'Link (optional)',
        'push' => 'Also send as a push notification to the mobile app (Firebase)',
        'url_hint' => 'A web link opened by the app when the user taps the notification.',
    ],

    'audiences' => [
        'customers' => 'All customers',
        'vendors' => 'All vendors',
        'all' => 'Customers and vendors',
        'user' => 'One user',
    ],

    'columns' => [
        'title' => 'Title',
        'audience' => 'Sent to',
        'recipients' => 'Recipients',
        'push' => 'Push',
        'date' => 'Date',
    ],

    'messages' => [
        'all_read' => 'All notifications are marked as read.',
        'deleted' => 'Notification deleted.',
        'queued' => 'The message is being sent to the selected users.',
    ],

    // The texts of the automatic notifications (both languages are saved with every notification).
    'events' => [
        'new_order' => [
            'title' => 'New order :number',
            'body' => 'Customer: :customer - Total: :total',
        ],
        'new_order_vendor' => [
            'title' => 'New order :number',
            'body' => 'A new order contains your products.',
        ],
        'order_updated_vendor' => [
            'title' => 'Order :number updated',
            'body' => 'Status: :status_en - Delivery: :delivery_en',
        ],
        'new_return' => [
            'title' => 'New return request',
            'body' => 'Return :number for order :order is waiting for review.',
        ],
        'new_store' => [
            'title' => 'New store waiting for approval',
            'body' => 'The store ":store" needs your review.',
        ],
        'store_approved' => [
            'title' => 'Your store was approved',
            'body' => 'The store ":store" is now approved.',
        ],
        'store_rejected' => [
            'title' => 'Your store was rejected',
            'body' => 'The store ":store" was not approved.',
        ],
        'new_message' => [
            'title' => 'New contact message',
            'body' => ':name wrote: :subject',
        ],
    ],
];
