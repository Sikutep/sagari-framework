<?php

namespace Core;

class Database
{
    protected $pdo;
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->connect();
    }
    
    protected function connect()
    {
        $driver = $this->config['driver'];
        $host = $this->config['host'];
        $port = $this->config['port'];
        $database = $this->config['database'];
        $username = $this->config['username'];
        $password = $this->config['password'];
        $charset = $this->config['charset'] ?? 'utf8mb4';
        
        $dsn = "{$driver}:host={$host};port={$port};dbname={$database};charset={$charset}";
        
        $this->pdo = new \PDO($dsn, $username, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }
    
    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function select($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function selectOne($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }
    
    public function insert($sql, $params = [])
    {
        $this->query($sql, $params);
        return $this->pdo->lastInsertId();
    }
    
    public function update($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function delete($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }
    
    public function commit()
    {
        $this->pdo->commit();
    }
    
    public function rollback()
    {
        $this->pdo->rollback();
    }
    
    public function getPdo()
    {
        return $this->pdo;
    }
}