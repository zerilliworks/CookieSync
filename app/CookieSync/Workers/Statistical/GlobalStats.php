<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 4/12/14
// Time: 9:59 PM
// For: CookieSync


namespace CookieSync\Workers\Statistical;


use CookieSync\Stat\GlobalCookieCounter;
use Game;
use Illuminate\Support\Facades\Cache;
use Save;

class GlobalStats {

    public function fire($job, $data)
    {
        try {
        $counter = new GlobalCookieCounter(new Game, new Save);
        Cache::remember('global_cookie_count', 1, $counter->calculateEverySave());
        } catch (\Exception $e) {
            App::abort('500');
        }

        $job->delete();
    }

} 
