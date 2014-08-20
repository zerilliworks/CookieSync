<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 8/19/14
// Time: 11:22 PM
// For: CookieSync


namespace CookieSync\Authentication;


use Illuminate\Mail\Mailer;
use Illuminate\Support\ServiceProvider;

class EmailManagerServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('email_manager', 'CookieSync\Authentication\EmailManager');
    }
}