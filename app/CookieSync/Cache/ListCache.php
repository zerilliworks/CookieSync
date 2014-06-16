<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 4/30/14
// Time: 5:20 PM
// For: CookieSync


namespace CookieSync\Cache;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class ListCache {

    protected $regenProcedure;
    protected $collectionRegenProcedure;
    protected $key;
    protected $length = 30;

    public function push($item)
    {
        Redis::lpush($this->key, $item);
        return $this;
    }

    public function store($item)
    {
        $this->push($item)->trim(30);
        return $this;
    }

    public function fetch()
    {
        return Redis::get($this->key);
    }

    public function clear()
    {
        $this->trim(0);
        return $this;
    }

    public function trim($length = null)
    {
        if($length === null)
        {
            Redis::trim($this->key, 0, $this->length);

        }
        Redis::trim($this->key, 0, $length);
        return $this;
    }

    public function regenFromCollection(Collection $collection)
    {
        $this->clear();
        foreach ($collection as $item) {
            $this->store($item);
        }
        return $this;
    }

    /**
     * Use a closure to operate on elements of a collection before storing them.
     * Using a plain closure will loop over each element in the collection and
     * return it after modification. If the first parameter of the closure is
     * type-hinted as a collection, then the entire collection will be passed
     * into the closure and it will be called only once.
     *
     * @param Collection $collection
     * @param callable $procedure
     * @throws \InvalidArgumentException
     */
    public function regenFromCollectionWithProcedure(Collection $collection, callable $procedure)
    {
        $introspector = new \ReflectionFunction($procedure);
        if($introspector->getNumberOfParameters() < 2) {
            throw new \InvalidArgumentException("The closure passed to ListCache::regenFromCollectionWithProcedure had too few input arguments.");
        }

        // Check if the first parameter is type-hinted as a collection.
        if($introspector->getParameters()[0]->getClass() == "Collection")
        {
            call_user_func_array($procedure, [$collection, &$this]);
        } else {
            foreach($collection as $item) {
                call_user_func_array($procedure, [$item, &$this]);
            }
        }

    }

    /**
     * @param          $key
     * @param callable $procedure
     *
     * @return $this
     */
    public function regen($key, callable $procedure = null)
    {
        if ($procedure) {
            call_user_func_array($procedure, [&$this]);
        }
        else {
            call_user_func_array($this->regenProcedure, [&$this]);
        }

        return $this;
    }

    public function regenWith(callable $procedure)
    {
        $this->regenProcedure = $procedure;
        return $this;
    }

    public function withKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function size($length)
    {
        if($length <= 0 || !is_integer($length))
        {
            throw new \InvalidArgumentException("A ListCache size must be an integer greater than zero.");
        }
        $this->length = $length;
        return $this;
    }

} 
