<?php

// serve.php

$port = $argv[1] ?? 8000; // Ambil port dari argumen, default 8000
$public_dir = __DIR__ . '/public';

echo "Laravel-like development server started on http://127.0.0.1:$port\n";
echo "Document root is $public_dir\n";
echo "Press Ctrl+C to quit.\n";

// Jalankan built-in server PHP dari folder public
$command = "php -S 127.0.0.1:$port -t \"$public_dir\"";

// Tambahkan server.php jika ingin perilaku routing mirip Laravel
// $command = "php -S 127.0.0.1:$port -t \"$public_dir\" server.php";

passthru($command);