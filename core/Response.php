<?php

namespace Core;

class Response
{
    protected $content;
    protected $statusCode = 200;
    protected $headers = [];
    
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }
    
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }
    
    public function json($data, $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json');
        $this->setContent(json_encode($data));
        return $this;
    }
    
    public function send()
    {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
        
        echo $this->content;
    }
}