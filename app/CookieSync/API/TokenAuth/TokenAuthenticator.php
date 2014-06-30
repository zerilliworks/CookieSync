<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 6/30/14
// Time: 12:39 AM
// For: CookieSync


namespace CookieSync\API\TokenAuth;


class TokenAuthenticator {

    protected $driver;

    function __construct(TokenAuthDriverInterface $driver)
    {
        $this->driver = $driver;
    }


} 
