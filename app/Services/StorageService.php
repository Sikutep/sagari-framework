<?php

namespace App\Services;

use Core\Application;

class StorageService {

    protected $config;
    protected $uploadsPath;

    public function __construct()
    {
        $this->config = require dirname(__DIR__, 2) . '/config/storage.php';
        $this->uploadsPath = $this->config['uploads'];
        
        if (!is_dir($this->uploadsPath)) {
            mkdir($this->uploadsPath, 0755, true);
        }
    }
    
    public function store($file, $directory = '')
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('File upload failed');
        }
        
        if ($file['size'] > $this->config['max_file_size']) {
            throw new \Exception('File size exceeds maximum allowed size');
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        if (!in_array(strtolower($extension), $this->config['allowed_extensions'])) {
            throw new \Exception('File type not allowed');
        }
        
        $filename = $this->generateUniqueFilename($extension);
        $destination = $this->uploadsPath . '/' . $directory;
        
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $fullPath = $destination . '/' . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new \Exception('Failed to move uploaded file');
        }
        
        return [
            'filename' => $filename,
            'path' => $directory . '/' . $filename,
            'size' => $file['size'],
            'type' => $file['type']
        ];
    }
    
    public function delete($path)
    {
        $fullPath = $this->uploadsPath . '/' . $path;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }
    
    public function exists($path)
    {
        return file_exists($this->uploadsPath . '/' . $path);
    }
    
    public function get($path)
    {
        $fullPath = $this->uploadsPath . '/' . $path;
        
        if (!file_exists($fullPath)) {
            return null;
        }
        
        return file_get_contents($fullPath);
    }
    
    protected function generateUniqueFilename($extension)
    {
        return uniqid() . '_' . time() . '.' . $extension;
    }
    
    public function url($path)
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
        return $config['url'] . '/storage/uploads/' . $path;
    }
}