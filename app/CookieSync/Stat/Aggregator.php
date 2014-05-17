<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/30/14
// Time: 9:13 PM
// For: CookieSync


namespace CookieSync\Stat;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use User;

class Aggregator {

    public static function careerCookies(User $user)
    {
        $careerCookies = '0';
        // Calculate the total cookies earned in all games
        foreach ($user->games()->get() as $game) {
            $careerCookies = bcadd($game->latestSave()->cookies(), $careerCookies);
        }
        return $careerCookies;
    }

    public static function careerSaves(User $user)
    {
        return $user->saves()->count();
    }

    public static function careerGames(User $user)
    {
        return $user->games()->count();
    }

    public static function cookieHistory(User $user, $sample = 30)
    {
        $history = new Collection;

        if(!Redis::exists("users:$user->id:career:updated_at"))
        {
            $lastUpdate = Carbon::now();
            Redis::set("users:$user->id:career:updated_at", $lastUpdate);
            Redis::expire("users:$user->id:career:updated_at", Carbon::now()->addHour()->diffInSeconds());
            $query = $user->saves()->take($sample);
        } else {
            $lastUpdate = Redis::get("users:$user->id:career:updated_at");
            $query = $user->saves()->take($sample)->where('created_at', '<', $lastUpdate);
        }

        foreach($query->get() as $save) {
            $history->push([$save->created_at, $save->cookies()]);
        }

        return $history;
    }



} 
