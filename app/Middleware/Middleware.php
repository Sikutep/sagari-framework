<?php

namespace App\Middleware;

use Core\Request;

abstract class Middleware
{
    abstract public function handle(Request $request, $next);
}