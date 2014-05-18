<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 4/26/14
// Time: 11:01 AM
// For: CookieSync


namespace CookieSync\Tools\Observers;


use Illuminate\Support\ServiceProvider;
use Save;

class ObserversServiceProvider extends ServiceProvider {

    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    public function boot()
    {
         Save::observe(new SaveModelObserver);
    }
}
