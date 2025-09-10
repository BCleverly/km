<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Historical Encryption Keys
    |--------------------------------------------------------------------------
    |
    | This array contains historical app keys that can be used to decrypt
    | files that were encrypted with previous app keys. When you rotate
    | your app key, add the old key here with a unique identifier.
    |
    | IMPORTANT: Store these keys securely and never commit them to version control.
    | Consider using environment variables or a secure key management service.
    |
    | Format: 'key_id' => 'base64:encoded_key'
    |
    */

    'historical_keys' => [
        // Example:
        // '2024-01-15-key-rotation' => env('HISTORICAL_KEY_2024_01_15'),
        // '2024-06-01-key-rotation' => env('HISTORICAL_KEY_2024_06_01'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption Settings
    |--------------------------------------------------------------------------
    |
    | Configure encryption algorithm and settings
    |
    */

    'algorithm' => 'AES-256-CBC',
    'iv_length' => 16,
];