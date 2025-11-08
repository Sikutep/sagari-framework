<?php

namespace App\Middleware;


class CorsMiddleware extends Middleware
{
    public function handle($request, $next)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
        
        return $next($request);
    }
}