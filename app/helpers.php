<?php

use Core\Application;
use Core\Response;
use Core\Request;

if (!function_exists('app')) {
    function app($abstract = null) {
        if (is_null($abstract)) {
            return Application::getInstance();
        }
        return Application::getInstance()->container()->resolve($abstract);
    }
}

if (!function_exists('response')) {
    function response() {
        return new Response();
    }
}

if (!function_exists('request')) {
    function request() {
        return app('request');
    }
}

if (!function_exists('config')) {
    function config($key, $default = null) {
        $config = app('config');
        $parts = explode('.', $key);
        $value = $config;
        foreach ($parts as $part) {
            $value = $value[$part] ?? null;
            if ($value === null) {
                return $default;
            }
        }
        return $value;
    }
}

if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (strlen($value) > 1 && \str_starts_with($value, '"') && \str_ends_with($value, '"')) {
            return \substr($value, 1, -1);
        }

        return $value;
    }
}