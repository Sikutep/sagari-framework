<?php

use Core\Application;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\RateLimitMiddleware;


$app = Application::getInstance();
$router = $app->router();

$router->group(['prefix' => 'api/v1', 'middleware' => [CorsMiddleware::class, RateLimitMiddleware::class]], function($router) {
    
    // Public routes
    $router->get('/', 'ApiController@index');
    $router->post('/register', 'ApiController@store');
    
    // Protected routes
    $router->group(['middleware' => [AuthMiddleware::class]], function($router) {
        $router->get('/users', function($request) {
            return response()->json(['users' => []]);
        });
        
        $router->get('/users/{id}', function($request) {
            $id = $request->param('id');
            return response()->json(['user_id' => $id]);
        });
        
        $router->post('/users', 'ApiController@store');
        $router->put('/users/{id}', 'ApiController@store');
        $router->delete('/users/{id}', function($request) {
            $id = $request->param('id');
            return response()->json(['deleted' => $id]);
        });
    });
});
