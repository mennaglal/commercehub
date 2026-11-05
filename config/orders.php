<?php

return [

    'gateways' => [
        'myfatoorah' => [
            'name' => 'MyFatoorah',
            'enabled' => env('MYFATOORAH_ENABLED', false),
        ],

        'paymob' => [
            'name' => 'Paymob',
            'enabled' => env('PAYMOB_ENABLED', false),
        ],

        'paypal' => [
            'name' => 'PayPal',
            'enabled' => env('PAYPAL_ENABLED', false),
        ],
    ],

    'return_days' => 14,

];
