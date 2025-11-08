<?php

namespace Core;

class Container
{
    protected $bindings = [];
    protected $instances = [];
    
    public function bind($abstract, $concrete)
    {
        $this->bindings[$abstract] = $concrete;
    }
    
    public function singleton($abstract, $concrete)
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => true
        ];
    }
    
    public function resolve($abstract)
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        
        if (!isset($this->bindings[$abstract])) {
            return $this->buildClass($abstract);
        }
        
        $binding = $this->bindings[$abstract];
        
        if (is_array($binding) && isset($binding['shared'])) {
            $concrete = $binding['concrete'];
            $shared = $binding['shared'];
        } else {
            $concrete = $binding;
            $shared = false;
        }
        
        $object = is_callable($concrete) ? $concrete($this) : $this->buildClass($concrete);
        
        if ($shared) {
            $this->instances[$abstract] = $object;
        }
        
        return $object;
    }
    
    protected function buildClass($class)
    {
        if (!class_exists($class)) {
            throw new \Exception("Class {$class} does not exist");
        }
        
        $reflection = new \ReflectionClass($class);
        
        if (!$reflection->isInstantiable()) {
            throw new \Exception("Class {$class} is not instantiable");
        }
        
        $constructor = $reflection->getConstructor();
        
        if (is_null($constructor)) {
            return new $class;
        }
        
        $parameters = $constructor->getParameters();
        $dependencies = [];
        
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            
            if ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->resolve($type->getName());
            }
        }
        
        return $reflection->newInstanceArgs($dependencies);
    }
}