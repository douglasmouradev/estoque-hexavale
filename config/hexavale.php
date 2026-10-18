<?php

declare(strict_types=1);

return [
    'field_encryption_key' => env('FIELD_ENCRYPTION_KEY'),
    'field_blind_index_key' => env('FIELD_BLIND_INDEX_KEY'),
    'webauthn' => [
        'rp_id' => env('WEBAUTHN_RP_ID', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost'),
        'rp_name' => env('WEBAUTHN_RP_NAME', 'HexaVale Estoque'),
    ],
];
