<?php

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    return [
        'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'], // Define paths for CORS
        'allowed_methods' => ['*'], // Allow all HTTP methods
        'allowed_origins' => ['http://localhost:5173'], 
        'allowed_origins_patterns' => [],
        'access-control-allow-origins' => ['*'],
        'access-control-allow-credentials' => true,
        'allowed_headers' => ['Content-Type', 'X-Requested-With', 'X-XSRF-TOKEN', 'Authorization'],
        'exposed_headers' => [],
        'max_age' => 0,
        'supports_credentials' => true, // Required for cookies
    ];
    
    
    
    
    
    