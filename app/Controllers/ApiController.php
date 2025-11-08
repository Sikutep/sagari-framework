<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;

class ApiController extends Controller
{
    public function index()
    {
        return $this->success('API is running', [
            'version' => '1.0.0',
            'timestamp' => time()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|email'
        ]);
        
        // Process your data here
        
        return $this->success('Data created successfully', $data, 201);
    }
}