<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Play Store Configuration
    |--------------------------------------------------------------------------
    |
    | When the app is approved on Google Play (after the 10th-13th), change
    | status to 'active' and provide the store URL.
    | Status options: 'moderation' (disabled/info mode) or 'active' (direct link).
    |
    */
    'google_play_status' => env('GOOGLE_PLAY_STATUS', 'moderation'),
    'google_play_url' => env('GOOGLE_PLAY_URL', 'https://play.google.com/store/apps/details?id=com.airsen.app'),
    'admin_initial_password' => env('ADMIN_INITIAL_PASSWORD'),
];
