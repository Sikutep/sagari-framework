<?php

namespace App\Models;

use Core\Application;

abstract class Model
{
    protected $db;
    protected $table; 
    protected $primaryKey = 'id';
    protected $fillable = []; 

    public function __construct()
    {
        $this->db = Application::getInstance()->container()->resolve('db');
    }
    
    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->select($sql);
    }
    
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->selectOne($sql, [$id]);
    }
    
    public function where($conditions)
    {
        $where = [];
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $where[] = "{$key} = ?";
            $params[] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where);
        return $this->db->select($sql, $params);
    }
    
    public function create($data)
    {
        $data = $this->filterFillable($data);
        
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $id = $this->db->insert($sql, array_values($data));
        
        return $this->find($id);
    }
    
    public function update($id, $data)
    {
        $data = $this->filterFillable($data);
        
        $set = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $set[] = "{$key} = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = ?";
        $this->db->update($sql, $params);
        
        return $this->find($id);
    }
    
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->delete($sql, [$id]);
    }
    
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
}
