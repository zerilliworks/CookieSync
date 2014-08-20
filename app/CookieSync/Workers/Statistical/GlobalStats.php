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
use Illuminate\Support\Facades\Redis;
use Log;
use Save;

class GlobalStats {

    public function fire(Job $job, $data)
    {
        try {
            if(Redis::exists('global_count_busy')) {
                // Abort this, someone's already workin on it.
                Log::info("Skipping computation in " . get_class($this));
                $job->delete();
                return;
            }

            // Otherwise, call dibs on the computation
            Redis::pipeline(function($pipe) {
                $pipe->set('global_count_busy', 1);
                $pipe->expire('global_count_busy', 15 * 60);
            });

            Log::info('Computing cookie total...');
            $counter = new GlobalCookieCounter(new Game, new Save);

            if(!Cache::has('soft_global_cookie_count'))
            {
                $cookieTotal = $counter->calculateEverySave();
                Cache::add('soft_global_cookie_count',  $cookieTotal, 15);
                Cache::forever('sticky_global_cookie_count', $cookieTotal);
            }

            Redis::del('global_count_busy');
            Log::info('Computing done in worker ' . get_class($this));
        }
        catch (\Exception $e) {
            Log::error('Computing failed in worker '
                       . get_class($this)
                       . ' retried '
                       . $job->attempts()
                       . ' times with message: '
                       . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
    }

} 
