<?php

return [
    'driver' => env('CACHE_DRIVER', 'file'),
    'path' => BASE_PATH . '/storage/cache',
    'ttl' => env('CACHE_TTL', 3600),
];