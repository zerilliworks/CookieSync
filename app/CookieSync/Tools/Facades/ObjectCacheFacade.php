<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 5/17/14
// Time: 10:10 AM
// For: CookieSync


namespace CookieSync\Tools\Facades;


use Illuminate\Support\Facades\Facade;

class ObjectCacheFacade extends Facade {

    public static function getFacadeAccessor()
    {
        return 'cookiesync.object_cache';
    }

} 
