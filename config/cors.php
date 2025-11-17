<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'test', 'api/pokemons', 'api/pokemons/search'], // Add your route path
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000','http://localhost:3002'], // Your Next.js dev server URL
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];