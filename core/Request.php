<?php

namespace Core;

class Request
{
    protected $params = [];
    
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    // public function uri()
    // {
    //     $uri = $_SERVER['REQUEST_URI'];
    //     $position = strpos($uri, '?');
        
    //     if ($position !== false) {
    //         $uri = substr($uri, 0, $position);
    //     }
        
    //     return $uri;
    // }

    public function uri()
{
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $scriptName = $_SERVER['SCRIPT_NAME']; 


    $pos = strpos($requestUri, '?');
    if ($pos !== false) {
        $requestUri = substr($requestUri, 0, $pos);
    }


    $basePath = dirname($scriptName); 


    if (strpos($requestUri, $basePath) === 0) {
        $uri = substr($requestUri, strlen($basePath));
    } else {

        $uri = $requestUri;
    }

    $uri = ltrim($uri, '/');

    return $uri;
}
    
    public function input($key = null, $default = null)
    {
        $data = $this->all();
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }
    
    public function all()
    {
        if ($this->method() === 'GET') {
            return $_GET;
        }
        
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            return json_decode(file_get_contents('php://input'), true) ?? [];
        }
        
        return $_POST;
    }
    
    public function query($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        
        return $_GET[$key] ?? $default;
    }
    
    public function header($key, $default = null)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$key] ?? $default;
    }
    
    public function bearerToken()
    {
        $header = $this->header('Authorization', '');
        
        if (strpos($header, 'Bearer ') === 0) {
            return substr($header, 7);
        }
        
        return null;
    }
    
    public function setParams($params)
    {
        $this->params = $params;
    }
    
    public function param($key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
    
    public function file($key)
    {
        return $_FILES[$key] ?? null;
    }
    
    public function validate($rules)
    {
        $data = $this->all();
        $errors = [];
        
        foreach ($rules as $field => $ruleSet) {
            $ruleList = explode('|', $ruleSet);
            
            foreach ($ruleList as $rule) {
                if ($rule === 'required' && empty($data[$field])) {
                    $errors[$field][] = "The {$field} field is required";
                }
                
                if (strpos($rule, 'min:') === 0 && isset($data[$field])) {
                    $min = (int)substr($rule, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field][] = "The {$field} must be at least {$min} characters";
                    }
                }
                
                if (strpos($rule, 'max:') === 0 && isset($data[$field])) {
                    $max = (int)substr($rule, 4);
                    if (strlen($data[$field]) > $max) {
                        $errors[$field][] = "The {$field} must not exceed {$max} characters";
                    }
                }
                
                if ($rule === 'email' && isset($data[$field])) {
                    if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = "The {$field} must be a valid email";
                    }
                }
            }
        }
        
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
        
        return $data;
    }
}
