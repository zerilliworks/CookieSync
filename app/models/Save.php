<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 10/26/13
// Time: 11:11 PM
// For: CookieSync

class Save extends Eloquent {

    protected $fillable = array('save_data');
    public $gameData = array();

    public static function boot()
    {
        parent::boot();

        Save::restoring(function($model)
        {
            // FIXME: for some reason, this does not automatically decode game data.
            $model->decode();
            return true;
        });
    }


    public function user()
    {
        return $this->belongsTo('User');
    }

    public function isShared()
    {
        if($this->hasOne('SharedSave')) {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function makePublic()
    {
        $shr = new SharedSave();
        $shr->save_id = $this->id;
        $shr->save();
        return $shr;
    }

    public function sharedInstance()
    {
        return $this->hasOne('SharedSave');
    }

    public function data()
    {
        return $this->attributes['save_data'];
    }

    public function cookies($pretty = false)
    {
        if(!$pretty) {
            return $this->gameStat('banked_cookies');
        }
        else
        {
            return $this->prettyNumbers($this->gameStat('banked_cookies'));
        }
    }

    public function allTimeCookies()
    {
        return $this->prettyNumbers($this->gameStat('alltime_cookies'));
    }

    public function isGrandmapocalypse()
    {

    }

    /**
     * Decode the raw save data.
     *
     * Cookie Clicker saves are Base64-encoded strings delimited by a pipe ("|").
     * It is a serialized output of game variables, which due to the design of the
     * game are unlikely to change. This method decodes the string and explodes it
     * into PHP arrays; it then fills a gameData array with these data. Return true
     * if decoding was successful, or false if anything failed.
     *
     * See also: $this->gameStat()
     *
     * @return bool
     */
    public function decode()
    {
        try {
            $data = explode('|', base64_decode($this->data()));
            /**
             *  After exploding, the game data is broken up like this:
             *
             *  array[0] => game version
             *
             *  array[1] => null
             *
             *  array[2] => game dates : semicolon-delimited list of timestamps
             *           ---
             *           => [0 => startDate, 1 => fullDate, 2 => lastDate]
             *
             *
             *  array[3] => prefs : sequence of booleans, 0 or 1
             *           ---
             *           => [0 => particles?, 1 => numbers?, 2 => autosave?, 3 => milk?, 4 => fancyGraphics?]
             *
             *
             *  array[4] => game statistics : semicolon-delimited string of numbers
             *           ---
             *           => [0 => cookiesBaked, 1 => allTimeCookies, 2 => cookieClicks, 3 => allTimeGoldenClicks,
             *               4 => handmadeCookies, 5 => missedGoldenCookies, 6 => backgroundType, 7 => milkType,
             *               8 => cookiesReset, 9 => elderWrath, 10 => pledges, 11 => pledgeTimeLeft, 12 => nextResearch,
             *               13 => researchTimeLeft, 14 => timesReset, 15 => goldenCookieClicks
             *              ]
             *
             *
             *  array[5] => game buildings : a semicolon-delimited list of buildings, each section containing
             *              a comma-separated list of numbers
             *           ---
             *           => [0 => cursors[0 => howManyOwned, 1 => howManyBought, 2 => cookiesProduced, 3 => special?],
             *               1 => grandmas[0 => howManyOwned, 1 => howManyBought, 2 => cookiesProduced, 3 => special?],
             *               2 => farms[...],
             *               3 => factories[...],
             *               4 => mines[...],
             *               5 => shipments[...],
             *               6 => labs[...],
             *               7 => portals[...],
             *               8 => timemachines[...],
             *               9 => condensers[...],
             *              ]
             *
             *
             *  array[6] => game upgrades
             *  array[7] => game achievements
             */

            $dates = explode(';', $data[2]);
            $cookieStats = explode(';', $data[4]);

            $this->gameData['game_version'] = $data[0];
            $this->gameData['date_started'] = \Carbon\Carbon::createFromTimestamp($dates[0]);
            $this->gameData['date_saved'] = \Carbon\Carbon::createFromTimestamp($dates[2]);
            $this->gameData['banked_cookies'] = $cookieStats[0];
            $this->gameData['alltime_cookies'] = $cookieStats[1];
            $this->gameData['cookie_clicks'] = $cookieStats[2];
            $this->gameData['alltime_golden_cookie_clicks'] = $cookieStats[3];
            $this->gameData['handmade_cookies'] = $cookieStats[4];
            $this->gameData['missed_golden_cookies'] = $cookieStats[5];
            $this->gameData['background_type'] = $cookieStats[6];
            $this->gameData['milk_type'] = $cookieStats[7];
            $this->gameData['cookies_reset'] = $cookieStats[8];
            $this->gameData['elder_wrath'] = $cookieStats[9];
            $this->gameData['pledge_count'] = $cookieStats[10];
            $this->gameData['pledge_time_left'] = $cookieStats[11];
            $this->gameData['next_research'] = $cookieStats[12];
            $this->gameData['research_time_left'] = $cookieStats[13];
            $this->gameData['times_reset'] = $cookieStats[14];
            $this->gameData['golden_cookie_clicks'] = $cookieStats[15];

            $buildings = explode(';', $data[5]);
            $cursors = explode(',', $buildings[0]);
            $grandmas = explode(',', $buildings[1]);
            $farms = explode(',', $buildings[2]);
            $factories = explode(',', $buildings[3]);
            $mines = explode(',', $buildings[4]);
            $shipments = explode(',', $buildings[5]);
            $alchemyLabs = explode(',', $buildings[6]);
            $portals = explode(',', $buildings[7]);
            $timeMachines = explode(',', $buildings[8]);
            $condensers = explode(',', $buildings[9]);

            $this->gameData['buildings.cursors']        = $cursors[0];
            $this->gameData['buildings.grandmas']       = $grandmas[0];
            $this->gameData['buildings.farms']          = $farms[0];
            $this->gameData['buildings.factories']      = $factories[0];
            $this->gameData['buildings.mines']          = $mines[0];
            $this->gameData['buildings.shipments']      = $shipments[0];
            $this->gameData['buildings.labs']           = $alchemyLabs[0];
            $this->gameData['buildings.portals']        = $portals[0];
            $this->gameData['buildings.time_machines']  = $timeMachines[0];
            $this->gameData['buildings.condensers']     = $condensers[0];

            /** !==============================================================!
             *
             *  TODO: implement a PHP equivalent of Cookie Clicker's (un)compressBin methods.
             *        PHP is not privy to the same string manipulation methods as JavaScript,
             *        so (un)compressing arrays like CC does requires some finagling.
             * */

            return true;
        } catch (ErrorException $e) {
            return false;
        }

    }

    public function allStats()
    {
        return $this->gameStat('buildings.cursors') . " Cursors" . "\n" .
        $this->gameStat('buildings.grandmas')  . " Grandmas" . "\n" .
        $this->gameStat('buildings.farms')  . " Farms" . "\n" .
        $this->gameStat('buildings.factories') . " Factories" . "\n" .
        $this->gameStat('buildings.mines') . " Mines" . "\n" .
        $this->gameStat('buildings.shipments') . " Shipments" . "\n" .
        $this->gameStat('buildings.labs') . " Labs" . "\n" .
        $this->gameStat('buildings.portals') . " Portals" . "\n" .
        $this->gameStat('buildings.time_machines') . " T.M.s" . "\n" .
        $this->gameStat('buildings.condensers') . " Condensers";
    }

    public function gameStat($name)
    {
        if(isset($this->gameData[$name]))
        {
            return $this->gameData[$name];
        }
        else
        {
            return null;
        }
    }

    private function uncompress($value)
    {
        // TODO: implement de-compression logic for cookie clicker property arrays
    }

    private function prettyNumbers($num)
    {
        $numstring = strval($num);
        if(strpos($numstring, '.'))
        {
            $components = explode('.', $numstring);
            $vl = strrev($components[0]);
            $output = "";
            foreach (str_split($vl, 3) as $chunk){
                $output = strrev($chunk) . ',' . $output;
            }

            return substr($output, 0, -1) . "." . substr($components[1],0,2);
        }
        else
        {
            $vl = strrev($numstring);
            $output = "";
            foreach (str_split($vl, 3) as $chunk){
                $output = strrev($chunk) . ',' . $output;
            }

            return substr($output, 0, -1);
        }

    }
}