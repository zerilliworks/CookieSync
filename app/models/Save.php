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

    public function decode()
    {
        try {
            $data = explode('|', base64_decode($this->data()));
            /**
             *  array[0] => game version
             *  array[1] => null
             *  array[2] => game dates (startDate;fullDate;lastDate)
             *  array[3] => prefs (111111){particles?,numbers?,autosave?,autoupdate?,milk?,fancyGraphics?}
             *  array[4] => game statistics (cookies in bank;all-time cookies earned;cookie clicks;golden cookie clicks;
             *              handmade cookies;missed golden cookies;background type;milk type;cookies reset;elder wrath;
             *              pledges;pledge time left;next research;research time left;times reset;golden clicks this session;
             *  array[5] => game buildings (how many owned,how many bought,how many cookies produced,
             *                              is special unlocked?;) * number of buildings
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
        } catch (ErrorException $e) {

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