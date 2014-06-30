<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 6/30/14
// Time: 12:34 AM
// For: CookieSync


class LoggedInUserApiController extends ApiController {

    protected $user;

    function __construct()
    {
        $this->user = Auth::user();
    }



} 
