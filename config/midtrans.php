<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    'append_notif_url' => env('MIDTRANS_APPEND_NOTIF_URL'),
    'cainfo' => env('MIDTRANS_CAINFO', null), // Custom CA certificate path
];