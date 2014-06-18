<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/28/14
// Time: 2:02 AM
// For: CookieSync

Event::listen('cookiesync.*', function($data)
{
    Log::debug('Event '. Event::firing() .' fired.');
});


Event::listen('cookiesync.logged_in', function($data)
{
    if($ppl = User::whereName($data)->first()->preferred_pagination_length)
    {
        Session::put('pagination_length', $ppl);
    } else {
        Session::put('pagination_length', 30);
    }
});
