<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 6/9/14
// Time: 1:48 PM
// For: CookieSync


namespace CookieSync\Events;

use Event;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

/**
 * Class EventSubServiceProvider
 *
 * @package CookieSync\Events
 */
class EventSubServiceProvider extends ServiceProvider {

    public function boot()
    {
        // Event::subscribe(App::make('SaveCacheListener'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('SaveCacheListener', function() {
            new SaveCacheListener();
        });
//        $this->app->bind('UserCacheListener', function() {
//            new UserCacheListener();
//        });
    }
}