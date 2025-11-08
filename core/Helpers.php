<?php

if (!function_exists('env')) {
    function env($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('response')) {
    function response() {
        return Core\Application::getInstance()->container()->resolve('response');
    }
}

if (!function_exists('request')) {
    function request() {
        return Core\Application::getInstance()->container()->resolve('request');
    }
}