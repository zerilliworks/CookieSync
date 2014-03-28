<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/28/14
// Time: 2:02 AM
// For: CookieSync

Event::listen('cookiesync.pulse.*', function($event) {
    return BrainSocket::message(Event::firing(), ['message'=>'Heads up.']);
});