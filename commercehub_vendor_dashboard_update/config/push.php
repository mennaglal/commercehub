<?php

return [
    // false = notifications are only saved in the database (the in-app inbox) and nothing is sent to Firebase.
    // Set FCM_ENABLED=true in .env after you finished the Firebase setup (see README-notifications.md).
    'enabled' => env('FCM_ENABLED', false),
];
