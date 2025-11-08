<?php

namespace Core;

class Router
{
    protected $routes = [];
    protected $middlewares = [];
    protected $groupStack = [];
    protected $container;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function get($uri, $action)
    {
        return $this->addRoute('GET', $uri, $action);
    }

    // Tambahkan di akhir class Router, sebelum kurawal penutup
public function getRoutesForDebug()
{
    return $this->routes;
}
    
    public function post($uri, $action)
    {
        return $this->addRoute('POST', $uri, $action);
    }
    
    public function put($uri, $action)
    {
        return $this->addRoute('PUT', $uri, $action);
    }
    
    public function patch($uri, $action)
    {
        return $this->addRoute('PATCH', $uri, $action);
    }
    
    public function delete($uri, $action)
    {
        return $this->addRoute('DELETE', $uri, $action);
    }
    
    public function group($attributes, $callback)
    {
        $this->groupStack[] = $attributes;
        call_user_func($callback, $this);
        array_pop($this->groupStack);
    }
    
    protected function addRoute($method, $uri, $action)
    {
        $uri = $this->applyGroupPrefix($uri);
        $middlewares = $this->getGroupMiddlewares();
        
        $route = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'middlewares' => $middlewares
        ];
        
        $this->routes[] = $route;
        
        return $this;
    }
    
    protected function applyGroupPrefix($uri)
    {
        $prefix = '';
        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
        }
        
        return trim($prefix . '/' . trim($uri, '/'), '/');
    }
    
    protected function getGroupMiddlewares()
    {
        $middlewares = [];
        foreach ($this->groupStack as $group) {
            if (isset($group['middleware'])) {
                $middlewares = array_merge($middlewares, (array)$group['middleware']);
            }
        }
        return $middlewares;
    }
    
    public function dispatch(Request $request)
    {
        $method = $request->method();
        $uri = trim($request->uri(), '/');
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->uriMatches($route['uri'], $uri, $params)) {
                $request->setParams($params);
                return $this->runRoute($route, $request);
            }
        }
        
        $response = new Response();
        $response->setStatusCode(404);
        $response->json(['error' => 'Route not found']);
        return $response;
    }

//     public function dispatch(Request $request)
// {
//     $method = $request->method();
//     $uri = trim($request->uri(), '/'); 

   

//     foreach ($this->routes as $index => $route) {
//         $route_method = $route['method'];
//         $route_uri = $route['uri'];



//         if ($route_method === $method) {
          
//             if ($this->uriMatches($route_uri, $uri, $params)) {
               
//                 $request->setParams($params);
//                 return $this->runRoute($route, $request);
//             } else {
//                 echo "  URI does not match.\n";
//             }
//         } else {
//             echo "  Method does not match.\n";
//         }
//     }

//     echo "No route matched.\n"; // Debug: Jika tidak ada yang cocok
//     $response = new Response();
//     $response->setStatusCode(404);
//     $response->json(['error' => 'Route not found']);
//     return $response;
// }
    
    protected function uriMatches($routeUri, $requestUri, &$params)
{
    $params = [];

    $routeUri = trim($routeUri, '/');

    $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routeUri);
    $pattern = '#^' . $pattern . '$#';

    if (preg_match($pattern, $requestUri, $matches)) {
        array_shift($matches);

        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $routeUri, $paramNames);

        foreach ($paramNames[1] as $index => $name) {
            $params[$name] = $matches[$index] ?? null;
        }

        return true;
    }

    return false;
}
    
    protected function runRoute($route, Request $request)
    {
        $middlewares = $route['middlewares'];
        
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function($next, $middleware) {
                return function($request) use ($next, $middleware) {
                    $middlewareInstance = new $middleware();
                    return $middlewareInstance->handle($request, $next);
                };
            },
            function($request) use ($route) {
                return $this->callAction($route['action'], $request);
            }
        );
        
        return $pipeline($request);
    }
    
    protected function callAction($action, Request $request)
    {
        if (is_callable($action)) {
            return call_user_func($action, $request);
        }
        
        if (is_string($action)) {
            list($controller, $method) = explode('@', $action);
            $controller = "App\\Controllers\\{$controller}";
            
            if (!class_exists($controller)) {
                throw new \Exception("Controller {$controller} not found");
            }
            
            $instance = $this->container->resolve($controller);
            
            if (!method_exists($instance, $method)) {
                throw new \Exception("Method {$method} not found in {$controller}");
            }
            
            return $instance->$method($request);
        }
        
        throw new \Exception("Invalid route action");
    }
}