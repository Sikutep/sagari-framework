<?php

namespace Core;

abstract class Controller
{
    protected function json($data, $statusCode = 200)
    {
        return response()->json($data, $statusCode);
    }
    
    protected function success($message, $data = [], $statusCode = 200)
    {
        return $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
    
    protected function error($message, $statusCode = 400)
    {
        return $this->json([
            'success' => false,
            'error' => $message
        ], $statusCode);
    }
}