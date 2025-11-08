<?php

namespace App\Services;

class CacheService
{
    protected $cachePath;
    protected $ttl;
    
    public function __construct()
    {
        $config = require BASE_PATH . '/config/cache.php';
        $this->cachePath = $config['path'];
        $this->ttl = $config['ttl'];
        
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }
    
    public function get($key)
    {
        $file = $this->getFilePath($key);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = json_decode(file_get_contents($file), true);
        
        if ($data['expires_at'] < time()) {
            unlink($file);
            return null;
        }
        
        return $data['value'];
    }
    
    public function put($key, $value, $ttl = null)
    {
        $file = $this->getFilePath($key);
        $ttl = $ttl ?? $this->ttl;
        
        $data = [
            'value' => $value,
            'expires_at' => time() + $ttl
        ];
        
        file_put_contents($file, json_encode($data));
        
        return true;
    }
    
    public function forget($key)
    {
        $file = $this->getFilePath($key);
        
        if (file_exists($file)) {
            return unlink($file);
        }
        
        return false;
    }
    
    public function flush()
    {
        $files = glob($this->cachePath . '/*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }
    
    protected function getFilePath($key)
    {
        return $this->cachePath . '/' . md5($key) . '.cache';
    }
    
    public function remember($key, $ttl, $callback)
    {
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        $this->put($key, $value, $ttl);
        
        return $value;
    }
}