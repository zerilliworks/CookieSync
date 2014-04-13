<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 4/12/14
// Time: 9:59 PM
// For: CookieSync


namespace CookieSync\Workers\Statistical;

use CookieSync\Stat\GlobalCookieCounter;
use Game;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Cache;
use Log;
use Save;

class GlobalStats {

    public function fire(Job $job, $data)
    {
        try {
            Log::info('Computing cookie total...');
            $counter = new GlobalCookieCounter(new Game, new Save);

            if(!Cache::has('soft_global_cookie_count'))
            {
                $cookieTotal = $counter->calculateEverySave();
                Cache::add('soft_global_cookie_count',  $cookieTotal, 15);
                Cache::forever('sticky_global_cookie_count', $cookieTotal);
            }



            Log::info('Computing done in worker ' . get_class($this));
        }
        catch (\Exception $e) {
            Log::error('Computing failed in worker '
                       . get_class($this)
                       . ' retried '
                       . $job->attempts()
                       . ' times with message: '
                       . $e->getMessage());
        }
    }

} 
