<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 6/30/14
// Time: 12:41 AM
// For: CookieSync


namespace CookieSync\API\TokenAuth;


interface TokenAuthDriverInterface {

    public function storeToken($token, $userId);
    public function getToken($token);
    public function removeToken($token);
    public function blockToken($token);
    public function allowToken($token);
    public function hasToken($token);
    public function userForToken($token);
    public function countTokens();

} 
