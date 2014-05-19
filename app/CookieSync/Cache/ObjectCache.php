<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 5/17/14
// Time: 9:47 AM
// For: CookieSync


namespace CookieSync\Cache;

use Illuminate\Support\Facades\Redis;

class ObjectCache {

    public function __construct()
    {

    }

    public function ping()
    {
        return "We are here.";
    }

    public function store($key, $object, $raw = false)
    {
        if(!is_object($object))
        {
            throw new \InvalidArgumentException('ObjectCache cannot store value of type ' . gettype($object));
        }

        if($raw) {
            Redis::hset("$key:_raw", serialize($object));
        }

        $reflector = new \ReflectionObject($object);

        Redis::pipeline(function($pipe) use ($key, $reflector, $object)
        {
            // start by storing some class metadata
            $pipe->hset($key, 'class', $reflector->getName());
//            $pipe->hset($key, 'parent', $reflector->getParentClass());
            $pipe->hset($key, 'user_defined', $reflector->isUserDefined());
            $pipe->hset($key, 'file', $reflector->getFileName());

            // Store properties
            foreach($reflector->getProperties() as $property)
            {
                $pn = $property->getName();
                $pipe->hset($key, "_property:$pn", $object->$pn);
            }

            // Store
        });

    }

    public function get($key)
    {
        if(Redis::exists("$key:_raw"))
        {
            return unserialize(Redis::hget("$key:_raw"));
        }
    }

} 
