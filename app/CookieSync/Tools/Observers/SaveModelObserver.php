<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 4/26/14
// Time: 11:01 AM
// For: CookieSync


namespace CookieSync\Tools\Observers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class SaveModelObserver
 * @package CookieSync\Tools\Observers
 */
class SaveModelObserver {

    /**
     * @param $model
     */
    public function created($model)
    {
        if(Auth::check())
        {
            $uid = Auth::user()->id;
            // Update the user's cookie total, regen if necessary.
            Cache::remember("users:$uid:cookies", 5, function() {
                $careerCookies = '0';
                // Calculate the total cookies earned in all games
                foreach (Auth::user()->games as $game) {
                    $careerCookies = bcadd($game->latestSave()->cookies(), $careerCookies);
                }
                return $careerCookies;
            });

            // Add the new save data to the aggregate cookie count
            Cache::put("users:$uid:cookies", bcadd(Cache::get("users:$uid:cookies"), $model->cookies()), 5);
        }
    }

} 
