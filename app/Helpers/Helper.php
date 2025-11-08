<?php

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        global $app;
        
        $keys = explode('.', $key);
        $config = $app->container()->resolve('config');
        
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                return $default;
            }
            $config = $config[$k];
        }
        
        return $config;
    }
}

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            echo '';
            var_dump($var);
            echo '';
        }
        die();
    }
}

if (!function_exists('response')) {
    function response()
    {
        return new \Core\Response();
    }
}

if (!function_exists('json_response')) {
    function json_response($data, $statusCode = 200)
    {
        return response()->json($data, $statusCode);
    }
}

if (!function_exists('logger')) {
    function logger($message, $level = 'info')
    {
        $logPath = BASE_PATH . '/storage/logs/app.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}\n";
        
        file_put_contents($logPath, $logMessage, FILE_APPEND);
    }
}

if (!function_exists('abort')) {
    function abort($statusCode, $message = '')
    {
        http_response_code($statusCode);
        
        if ($message) {
            echo json_encode(['error' => $message]);
        }
        
        exit;
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '')
    {
        return BASE_PATH . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('storage_path')) {
    function storage_path($path = '')
    {
        return BASE_PATH . '/storage' . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('public_path')) {
    function public_path($path = '')
    {
        return BASE_PATH . '/public' . ($path ? '/' . ltrim($path, '/') : '');
    }
}

