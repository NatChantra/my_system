<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // សម្រាប់សាកល្បង
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];
