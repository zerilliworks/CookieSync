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

        foreach($user->saves()->take($sample)->get() as $save) {
            $history->push([$save->created_at, $save->cookies()]);
        }

        return $history;
    }



} 
