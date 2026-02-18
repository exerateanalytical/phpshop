<?php

return [
    'app_name' => getenv('APP_NAME') ?: 'PharmaShop',
    'app_env' => getenv('APP_ENV') ?: 'production',
    'base_url' => rtrim(getenv('APP_BASE_URL') ?: '', '/'),
    'currency_symbol' => getenv('APP_CURRENCY_SYMBOL') ?: '$',

    'db_host' => getenv('DB_HOST') ?: '127.0.0.1',
    'db_name' => getenv('DB_NAME') ?: 'phpshop',
    'db_user' => getenv('DB_USER') ?: 'root',
    'db_pass' => getenv('DB_PASS') ?: '',

    'session_name' => getenv('SESSION_NAME') ?: 'PHPSHOPSESSID',
    'session_secure' => filter_var(getenv('SESSION_SECURE') ?: '0', FILTER_VALIDATE_BOOL),
    'session_lifetime' => (int) (getenv('SESSION_LIFETIME') ?: 7200),
];
