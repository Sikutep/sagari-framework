<?php

namespace App\Middleware;

use Core\Request;
use Core\Response;

class AuthMiddleware
{
    public function handle($request, $next)
    {
        // Extract Bearer token from Authorization header
        $authHeader = $request->getHeader('Authorization');
        $token = null;
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        }
        
        if (!$token) {
            $response = new Response();
            return $response->json(['error' => 'Unauthorized'], 401);
        }
        
        // Validate token here
        
        return $next($request);
    }
}