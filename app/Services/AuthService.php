<?php

namespace App\Services;

use App\Models\UserModel;

class AuthService
{
    private $user;
    
    public function __construct($user) {
        $this->user = $user;
    }
    
    public function register($data)
    {
        $existingUser = $this->user->findByEmail($data['email']);
        
        if ($existingUser) {
            throw new \Exception('Email already exists');
        }
        
        return $this->user->createUser($data);
    }
    
    public function login($email, $password)
    {
        if (!$this->user->verifyPassword($email, $password)) {
            throw new \Exception('Invalid credentials');
        }
        
        $user = $this->user->findByEmail($email);
        
       
        $token = bin2hex(random_bytes(32));
        
        return [
            'user' => $user,
            'token' => $token
        ];
    }
    
    public function validateToken($token)
    {

        return !empty($token);
    }
}
