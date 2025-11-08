<?php

namespace Core;

class Application
{
    protected static $instance;
    protected $basePath;
    protected $container;
    protected $router;
    protected $config = [];
    
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->container = new Container();
        $this->router = new Router($this->container);
        
        static::$instance = $this;
        
        $this->loadEnvironment();
        $this->loadConfiguration();
        $this->registerServices();
    }
    
    public static function getInstance()
    {
        return static::$instance;
    }
    
    protected function loadEnvironment()
    {
        $envFile = $this->basePath . '/.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                // Parse key=value
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value);
                    
                    // Remove quotes
                    $value = trim($value, '"\'');
                    
                    if (!array_key_exists($name, $_ENV)) {
                        putenv(sprintf('%s=%s', $name, $value));
                        $_ENV[$name] = $value;
                        $_SERVER[$name] = $value;
                    }
                }
            }
        }
    }
    
    protected function loadConfiguration()
    {
        $configFiles = ['app', 'database', 'cache', 'storage'];
        
        foreach ($configFiles as $file) {
            $path = $this->basePath . "/config/{$file}.php";
            if (file_exists($path)) {
                $this->config[$file] = require $path;
            }
        }
    }
    
    protected function registerServices()
    {
        // Register config
        $this->container->singleton('config', function() {
            return $this->config;
        });
        
        // Register database
        $this->container->singleton('db', function() {
            return new Database($this->config['database']);
        });
        
        // Register request
        $this->container->singleton('request', function() {
            return new Request();
        });
        
        // Register response
        $this->container->singleton('response', function() {
            return new Response();
        });
    }
    
    public function boot()
    {
        // Set error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', $this->config['app']['debug'] ? '1' : '0');
        
        // Set timezone
        date_default_timezone_set($this->config['app']['timezone'] ?? 'UTC');
        
        // Set default charset
        ini_set('default_charset', 'UTF-8');
        
        // Load routes
        $this->loadRoutes();
    }
    
   protected function loadRoutes()
{
    $routeFiles = ['web', 'api'];
    
    foreach ($routeFiles as $file) {
        $path = $this->basePath . "/routes/{$file}.php";
        if (file_exists($path)) {
            require $path;
        }
    }

    // Debug: Tampilkan jumlah route yang dimuat
    $router = $this->router; // Ambil router instance
    
}
    
    public function handleRequest()
    {
        try {
            $request = $this->container->resolve('request');
            $response = $this->router->dispatch($request);
            
            if ($response instanceof Response) {
                $response->send();
            }
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
   protected function handleException(\Exception $e)
{
    $statusCode = $e instanceof ValidationException ? 422 : 500; // <-- Ditentukan di sini
    
    $response = new Response();
    $response->setStatusCode($statusCode);
    
    if ($this->config['app']['debug']) {
        $response->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    } else {
        $response->json([
            'error' => 'Internal Server Error'
        ]);
    }
    
    $response->send();
    
    $this->logException($e);
}
    
    protected function logException(\Exception $e)
    {
        $logDir = $this->basePath . '/storage/logs';
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logPath = $logDir . '/error.log';
        
        $message = sprintf(
            "[%s] %s: %s in %s:%d\n%s\n%s\n\n",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString(),
            str_repeat('-', 80)
        );
        
        file_put_contents($logPath, $message, FILE_APPEND);
    }
    
    public function router()
    {
        return $this->router;
    }
    
    public function container()
    {
        return $this->container;
    }
    
    public function config($key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public function basePath($path = '')
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}