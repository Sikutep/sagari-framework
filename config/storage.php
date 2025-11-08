<?php

return [
    'uploads' => BASE_PATH . '/storage/uploads',
    'logs' => BASE_PATH . '/storage/logs',
    'max_file_size' => env('MAX_FILE_SIZE', 10485760), // 10MB
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
];