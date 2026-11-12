<?php

/*
| The settings of the public website (the storefront).
| Change the texts, the contact details and the links here. (Later they can come from a settings screen.)
*/
return [
    // Add ?v=... to the CSS/JS links: change it after you edit the files, so the browsers load the new ones.
    'version' => '1.0.0',

    'name' => ['ar' => 'متجري', 'en' => 'My Store'],
    'logo' => 'assets/web/img/logo.svg',

    'contact' => [
        'email' => 'support@example.com',
        'phone' => '+20 100 000 0000',
        'whatsapp' => '201000000000',            // international format, digits only
        'address' => ['ar' => 'القاهرة، مصر', 'en' => 'Cairo, Egypt'],
        'hours' => ['ar' => 'يوميًا من 9 ص إلى 9 م', 'en' => 'Every day, 9 AM - 9 PM'],
    ],

    // Leave a link empty to hide its icon.
    'social' => [
        'facebook' => '',
        'instagram' => '',
        'tiktok' => '',
        'x' => '',
        'youtube' => '',
    ],

    // The slugs of the pages written in the admin panel (Pages).
    'pages' => [
        'about' => 'about-us',
        'privacy' => 'privacy-policy',
        'returns' => 'return-policy',
        'terms' => 'terms-and-conditions',
    ],

    // The payment methods the customers can choose at the checkout.
    //   cod           cash on delivery
    //   bank_transfer needs the bank details below
    //   online        needs a connected payment gateway (Fatura, Tabby ...): keep it false until it is connected
    'payment_methods' => ['cod' => true, 'bank_transfer' => false, 'online' => false],

    // Gateways shown when "online" is on: key (the same keys as the admin panel) => label.
    'gateways' => ['fatura' => 'Fatura', 'tabby' => 'Tabby'],

    'bank_details' => [
        'ar' => "اسم البنك: ...\nرقم الحساب: ...\nاسم صاحب الحساب: ...",
        'en' => "Bank: ...\nAccount number: ...\nAccount holder: ...",
    ],

    'default_currency' => 'EGP',
];
