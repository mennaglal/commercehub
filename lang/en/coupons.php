<?php

return [
    'title' => 'Coupons',
    'add' => 'New coupon',
    'edit' => 'Edit coupon',
    'back' => 'Back to coupons',
    'save' => 'Save',
    'empty' => 'No coupons yet.',
    'general' => 'General',
    'unlimited' => 'Unlimited',
    'active' => 'Active',
    'inactive' => 'Inactive',

    'sections' => [
        'discount' => 'Discount',
        'limits' => 'Limits and dates',
        'categories' => 'Categories',
        'blogger' => 'Blogger and commission',
    ],

    'types' => [
        'fixed' => 'Fixed amount',
        'percentage' => 'Percentage (%)',
    ],

    'columns' => [
        'code' => 'Code',
        'discount' => 'Discount',
        'categories' => 'Categories',
        'owner' => 'Owner',
        'usage' => 'Used',
        'validity' => 'Valid',
        'status' => 'Status',
        'commission' => 'Commission',
    ],

    'fields' => [
        'code' => 'Coupon code',
        'type' => 'Discount type',
        'value' => 'Discount value',
        'max_discount' => 'Maximum discount',
        'max_discount_hint' => 'Only for percentage coupons. Leave empty for no cap.',
        'min_order_amount' => 'Minimum order amount',
        'min_order_hint' => 'Calculated on the products of the coupon categories only.',
        'usage_limit' => 'Usage limit',
        'usage_limit_hint' => 'Leave empty for unlimited.',
        'starts_at' => 'Starts at',
        'expires_at' => 'Expires at',
        'status' => 'Active',
        'categories' => 'Categories',
        'categories_hint' => 'The coupon works only on the products of these categories (and their subcategories). Hold Ctrl to select many.',
        'blogger' => 'Blogger',
        'general_option' => 'General coupon (no blogger)',
        'commission_type' => 'Commission type',
        'commission_value' => 'Commission value',
        'commission_hint' => 'Calculated on what the customer pays for the eligible products, after the discount. It becomes earned when the order is delivered.',
    ],

    'messages' => [
        'created' => 'Coupon created successfully.',
        'updated' => 'Coupon updated successfully.',
    ],

    'errors' => [
        'invalid' => 'This coupon is not valid.',
        'not_started' => 'This coupon is not active yet.',
        'expired' => 'This coupon has expired.',
        'limit_reached' => 'This coupon reached its usage limit.',
        'no_eligible_items' => 'This coupon does not apply to any product in the order.',
        'min_amount' => 'The eligible products must be at least :amount to use this coupon.',
    ],
];
