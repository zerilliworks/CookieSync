<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/19/14
// Time: 11:26 PM
// For: CookieSync


namespace CookieSync\Tools\Facades;


use Illuminate\Support\Facades\Facade;

class EmailManager extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'email_manager';
    }

} 