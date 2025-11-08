<?php

namespace App\Models;

class UserModel extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        return $this->db->selectOne($sql, [$email]);
    }
    
    public function createUser($data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        return $this->create($data);
    }
    
    public function verifyPassword($email, $password)
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password']);
    }
}