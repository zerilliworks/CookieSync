<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 5/12/14
// Time: 9:57 PM
// For: CookieSync


namespace CookieSync\Cache;


/**
 * Class CacheNamespacer
 * @package CookieSync\Cache
 */
class CacheNamespacer {

    public function users($id = null)
    {
        if($id !== null) {
            return "users:$id";
        } else {
            return "users";
        }
    }

    public function userSave($userId, $saveId)
    {
        return "users:$userId:saves:$saveId";
    }

} 
