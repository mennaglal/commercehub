<?php

return [
    'title' => 'Returns',
    'details' => 'Return details',
    'create' => 'Create return',
    'view' => 'View',
    'save' => 'Create return',
    'back' => 'Back to returns',
    'back_to_order' => 'Back to order',
    'search' => 'Search by return number, order number or customer',
    'filter' => 'Filter',
    'all' => 'All',
    'empty' => 'No returns found.',
    'none' => 'No returns for this order.',
    'yes' => 'Yes',
    'no' => 'No',

    'sections' => [
        'info' => 'Information',
        'items' => 'Returned items',
        'actions' => 'Actions',
        'returns' => 'Returns',
    ],

    'columns' => [
        'return_number' => 'Return #',
        'order' => 'Order',
        'customer' => 'Customer',
        'status' => 'Status',
        'reason' => 'Reason',
        'items' => 'Items',
        'refund' => 'Refund',
        'date' => 'Date',
        'product' => 'Product',
        'variant' => 'Variant',
        'sku' => 'SKU',
        'ordered' => 'Ordered',
        'remaining' => 'Can return',
        'unit_refund' => 'Refund per piece',
        'return_quantity' => 'Return qty',
        'quantity' => 'Qty',
        'restock' => 'Back to stock',
    ],

    'fields' => [
        'reason' => 'Reason',
        'customer_note' => 'Customer note',
        'admin_note' => 'Admin note',
        'refund_amount' => 'Refund amount',
        'refund_reference' => 'Refund reference',
        'refund_reference_hint' => 'Bank transfer number or gateway refund ID.',
        'max_refund' => 'Maximum',
        'items_hint' => 'Enter the quantity of each item the customer returns. The refund is calculated by the system, after the coupon discount.',
        'restock_hint' => 'Tick the pieces that are in good condition. Only these go back to the stock.',
        'shipping_note' => 'The shipping cost is not refunded.',
        'approved_at' => 'Approved at',
        'received_at' => 'Received at',
        'refunded_at' => 'Refunded at',
        'total_refund' => 'Total refund',
    ],

    'actions' => [
        'approve' => 'Approve',
        'reject' => 'Reject',
        'receive' => 'Mark as received',
        'refund' => 'Confirm refund',
        'reject_confirm' => 'Reject this return? This cannot be undone.',
        'refund_confirm' => 'Confirm that the money was refunded? This cannot be undone.',
        'final' => 'This status is final.',
    ],

    'statuses' => [
        'requested' => 'Requested',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'received' => 'Received',
        'refunded' => 'Refunded',
    ],

    'reasons' => [
        'defective' => 'Defective product',
        'wrong_item' => 'Wrong item received',
        'not_as_described' => 'Not as described',
        'changed_mind' => 'Changed my mind',
        'other' => 'Other',
    ],

    'messages' => [
        'created' => 'Return created successfully.',
        'updated' => 'Return updated successfully.',
    ],

    'errors' => [
        'not_delivered' => 'Only delivered orders can be returned.',
        'window_expired' => 'The return period (:days days after delivery) is over.',
        'invalid_item' => 'One of the items does not belong to this order.',
        'too_many' => 'You can return at most :available piece(s) of ":product".',
        'no_items' => 'Choose at least one item to return.',
        'status_not_allowed' => 'This status change is not allowed.',
        'refund_too_high' => 'The refund cannot be more than :max.',
        'order_has_returns' => 'This order already has customer returns. Manage them from the returns page.',
    ],
];
