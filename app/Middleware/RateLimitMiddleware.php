<?php

namespace App\Middleware;

use core\Request;
use core\Response;

class RateLimitMiddleware
{
    protected $maxAttempts = 60;
    protected $decayMinutes = 1;

    public function handle($request, $next)
    {
        $key = $this->getRateLimitKey($request);
        $attempts = $this->getAttempts($key);

        if ($attempts >= $this->maxAttempts) {
            $response = new Response();
            return $response->json(['error' => 'Too many requests'], 429);
        }

        $this->incrementAttempts($key);

        return $next($request);
    }

    protected function getRateLimitKey(Request $request)
    {
        return 'rate_limit:' . $_SERVER['REMOTE_ADDR'];
    }

    protected function getAttempts($key)
    {
        $cacheFile = BASE_PATH . '/storage/cache/' . md5($key);

        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data['expires_at'] > time()) {
                return $data['attempts'];
            }
        }

        return 0;
    }

    protected function incrementAttempts($key)
    {
        $cacheFile = BASE_PATH . '/storage/cache/' . md5($key);
        $attempts = $this->getAttempts($key) + 1;

        $data = [
            'attempts' => $attempts,
            'expires_at' => time() + ($this->decayMinutes * 60)
        ];

        file_put_contents($cacheFile, json_encode($data));
    }
}