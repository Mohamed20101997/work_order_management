<?php

return [
    'locale' => env('APP_LOCALE', 'en'),
    'fallback' => 'en',
    'available_locales' => ['en', 'ar'],
    'locale_direction' => [
        'en' => 'ltr',
        'ar' => 'rtl',
    ],
];
