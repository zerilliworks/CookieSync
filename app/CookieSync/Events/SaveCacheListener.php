<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 6/9/14
// Time: 1:45 PM
// For: CookieSync


namespace CookieSync\Events;


/**
 * Class SaveCacheListener
 *
 * @package CookieSync\Tools\Observers
 */
class SaveCacheListener {

    public function cacheNewSave($event)
    {
        var_dump($event);
    }

    public function subscribe($events)
    {
        $events->listen('cookiesync.newsave', 'SaveCacheListener@cacheNewSave');
    }

} 