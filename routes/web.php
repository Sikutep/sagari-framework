<?php

use Core\Application;

$app = Application::getInstance();
$router = $app->router();

$router->get('/', function($request) {
    return response()->json([
        'message' => 'Welcome to Backend Framework',
        'version' => '1.0.0'
    ]);
});

$router->get('/health', function($request) {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => time()
    ]);
});