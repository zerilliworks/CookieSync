<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 11/21/13
// Time: 11:11 PM
// For: CookieSync

namespace CookieSync\Stat;

use Carbon\Carbon;
use \Save;

class GameComparator {

    protected $baseGame;
    protected $differentGame;
    public $buildingDiffs = array();
    public $cookieDiff = 0;
    public $allTimeCookieDiff = 0;

    public static function getDiff(Save $game1, Save $game2)
    {
        $comparator = new GameComparator($game1);
        $comparator->compareTo($game2);


    }

    public function __construct(Save $baseGame)
    {
        $this->baseGame = $baseGame;
    }

    public function compareTo(Save $otherGame)
    {
        $this->differentGame = $otherGame;
        return $this;
    }

    public function compareWith(Save $otherGame)
    {
        return $this->compareTo($otherGame);
    }

    public function compareVersion()
    {
        return ($this->baseGame->gameStat('game_version') == $this->differentGame->gameStat('game_version'));
    }

    public function compareBuildings()
    {
        $buildingDiffCallback = function($base, $diff)
        {
            return intval($diff) - intval($base);
        };

        $mapped = array_map($buildingDiffCallback, $this->baseGame->buildings, $this->differentGame->buildings);
        return array_combine(array_keys($this->baseGame->buildings), $mapped);

    }

    public function compareBuilding($buildingName)
    {
        return intval($this->differentGame->buildings[$buildingName])
               - intval($this->baseGame->buildings[$buildingName]);
    }

    public function compareCookies()
    {
        return bcsub($this->differentGame->gameStat('banked_cookies'), $this->differentGame->gameStat('banked_cookies'));
    }

    public function compareAllTimeCookies()
    {
        return bcsub($this->differentGame->gameStat('alltime_cookies'), $this->differentGame->gameStat('alltime_cookies'));
    }

    public function compareStartDate()
    {
        return $this->baseGame->gameStat('date_started')->diff($this->differentGame->gameStat('date_started'));
    }

    public function compareSavedDate()
    {
        return $this->baseGame->gameStat('date_saved')->diff($this->differentGame->gameStat('date_saved'));
    }

    public function __get($var)
    {
        if(array_key_exists($var, $this->buildingDiffs))
        {
            return $this->buildingDiffs[$var];
        }
        else
        {
            switch($var)
            {
                case 'cookies':
                    return $this->cookieDiff;

                case 'allTimeCookies':
                    return $this->allTimeCookieDiff;
            }
        }
    }

}