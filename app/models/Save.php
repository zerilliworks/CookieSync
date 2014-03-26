<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 10/26/13
// Time: 11:11 PM
// For: CookieSync

/**
 * Class Save
 *
 * The Big One. Cookie Clicker saves are a bit complicated, but essentially are
 * serialized data. This class is a PHP implementation of Cookie Clicker's
 * save writing and importing functions.
 *
 * Because the numbers in Cookie Clicker can overflow most integer limits, we use
 * PHP's bcmath extensions to perform math on strings of numbers. It's impossible
 * otherwise: It totally blows out integers on 32-bit platforms, possibly signed
 * integers on 64-bit platforms.
 *
 * But you'd be crazy -- Nay, ABSOLUTELY BONKERS -- to collect that many cookies.
 */

use Carbon\Carbon;

class Save extends Eloquent implements \Illuminate\Support\Contracts\JsonableInterface {

    protected $fillable = array('save_data');
    protected $softDelete = true;
    public $gameData = array();
    public $decodedData = '';
    public $rawDataChunks = array();

    /**
     *
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            // Decode the raw save data
            $model->decode();

            // Assign actual Date/Time types to the start and save dates
            $model->started_at = $model->gameStat('date_started');
            $model->saved_at = $model->gameStat('date_saved');

            // Assign this save to a game if it doesn't already have one
            if(!$model->game_id)
            {
                // Find the appropriate game...
                $game = Auth::user()->games()->whereDateStarted($model->started_at)->first();

                if(!$game)
                {
                    $game = Auth::user()->games()->save(new Game(array(
                                                   'name' => "Game on ".  $model->started_at->toFormattedDateString(),
                                                   'date_started' => $model->started_at,
                                                   'date_saved' => with(new Carbon())->toDateTimeString(),
                                                   'cookie_history' => ''
                                              )));

                }
                $model->game_id = $game->id;

            }

            Event::fire('cookiesync.newsave', array(&$model));
        });

        static::deleting(function($model)
        {

            Event::fire('cookiesync.savedeleted', array(&$model));

            // Find out if this is the last save remaining in a game and
            // delete that game if it is.
            $saveCount = $model->game->saves()->count();

            if($saveCount <= 1)
            {
                $model->game->delete();
                Session::flash('deleted_game', true);
            }
        });
    }


    /**
     * Get the parent user of this save
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User');
    }

    /**
     * Get the parent game of this save
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game()
    {
        return $this->belongsTo('Game');
    }

    /**
     * Check whether this save is public or not
     *
     * @return bool
     */
    public function isShared()
    {
        return ($this->is_shared == 0)? false : true;
    }

    /**
     * Create a public link for a save. If it is already public, return the
     * existing shared link.
     *
     * @return bool|SharedSave
     */
    public function makePublic()
    {
        if($this->isShared())
        {
            // It's already shared, so don't repeat the process
            // just return the current shared instance.
            return $this->sharedInstance;
        }

        $shr = new SharedSave();
        $shr->save_id = $this->id;
        $this->sharedInstance()->save($shr);
        if($this->save())
        {
            $this->is_shared = 1;
            $this->save();

            Event::fire('cookiesync.saveshared', $this);

            return $shr;
        }
        else
        {
            return false;
        }
    }

    /**
     * Remove a public link for a save. If it is already private, do nothing.
     *
     * @return bool
     */
    public function makePrivate()
    {
        if(!$this->isShared())
        {
            // It's not shared, so forget about it.
            return false;
        }


        if($this->sharedInstance->delete())
        {
            $this->is_shared = 0;
            $this->save();

            Event::fire('cookiesync.saveprivatized', array($this));

            return true;

        }
        else
        {
            return false;
        }
    }

    /**
     * Get the instance of the shared save metadata
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sharedInstance()
    {
        return $this->hasOne('SharedSave');
    }

    /**
     * Get the original raw save data, just as Cookie Clicker exported it
     *
     * @return mixed
     */
    public function data()
    {
        return $this->attributes['save_data'];
    }

    /**
     * @internal param bool $pretty
     *
     * @return null|string
     */
    public function cookies()
    {
            return $this->gameStat('banked_cookies');
    }

    /**
     * Returns the all-time number of baked cookies
     *
     * @return integer
     */
    public function allTimeCookies()
    {
        return $this->gameStat('alltime_cookies');
    }

    /**
     * Get the number of Heavenly Chips for this save
     * @return null
     */
    public function heavenlyChips()
    {
        return $this->gameStat('prestige');
    }

    /**
     * Has ELDER WRATH taken hold in this save?
     *
     * @return bool
     */public function isGrandmapocalypse()
    {
        return ($this->gameStat('elder_wrath') >= 1) ? true : false;
    }

    /**
     * Decode the raw save data.
     *
     * Cookie Clicker saves are Base64-encoded strings delimited by a pipe ("|").
     * It is a serialized output of game variables, which due to the design of the
     * game are unlikely to mutate. This method decodes the string and explodes it
     * into PHP arrays; it then fills a gameData array with these data. Return true
     * if decoding was successful, or false if anything failed.
     *
     * This method also converts Cookie Clicker's compressed binary arrays into
     * expanded proper arrays of bits. It finally creates key/value pairs of the
     * achievement or upgrade ID and its name.
     *
     * See also: $this->gameStat()
     *
     * @return bool
     */
    public function decode()
    {
        try {
            $data = explode('|', $decodedData = base64_decode($this->data()));
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

            $this->decodedData = $decodedData;
            $this->rawDataChunks = $data;

            $dates = explode(';', $data[2]);
            $cookieStats = explode(';', $data[4]);
            $upgrades = $this->uncompressLargeBin($data[6]);
            $achievements = $this->uncompressLargeBin($data[7]);

            $this->gameData['game_version']                 = $data[0];
            $this->gameData['date_started']                 =
                \Carbon\Carbon::createFromTimestamp(substr($dates[0], 0, -3));
            $this->gameData['date_saved']                   =
                \Carbon\Carbon::createFromTimestamp(substr($dates[2], 0, -3));
            $this->gameData['banked_cookies']               = $cookieStats[0];
            $this->gameData['alltime_cookies']              = $cookieStats[1];
            $this->gameData['cookie_clicks']                = $cookieStats[2];
            $this->gameData['alltime_golden_cookie_clicks'] = $cookieStats[3];
            $this->gameData['handmade_cookies']             = $cookieStats[4];
            $this->gameData['missed_golden_cookies']        = $cookieStats[5];
            $this->gameData['background_type']              = $cookieStats[6];
            $this->gameData['milk_type']                    = $cookieStats[7];
            $this->gameData['cookies_reset']                = $cookieStats[8];
            $this->gameData['elder_wrath']                  = $cookieStats[9];
            $this->gameData['pledge_count']                 = $cookieStats[10];
            $this->gameData['pledge_time_left']             = $cookieStats[11];
            $this->gameData['next_research']                = $cookieStats[12];
            $this->gameData['research_time_left']           = $cookieStats[13];
            $this->gameData['times_reset']                  = $cookieStats[14];
            $this->gameData['golden_cookie_clicks']         = $cookieStats[15];

            $buildings    = explode(';', $data[5]);
            $cursors      = explode(',', $buildings[0]);
            $grandmas     = explode(',', $buildings[1]);
            $farms        = explode(',', $buildings[2]);
            $factories    = explode(',', $buildings[3]);
            $mines        = explode(',', $buildings[4]);
            $shipments    = explode(',', $buildings[5]);
            $alchemyLabs  = explode(',', $buildings[6]);
            $portals      = explode(',', $buildings[7]);
            $timeMachines = explode(',', $buildings[8]);
            $condensers   = explode(',', $buildings[9]);

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

            $this->gameData['upgrades.binary'] = $upgrades;
            $this->gameData['achievements.binary'] = $achievements;


            // Split upgrades into pairs of bits, split achievements into single bits
            // Upgrades are [bool, bool] which is [unlocked?, bought?]
            $upgradesArray = str_split($upgrades, 2);
            $achievementsArray = str_split($achievements);


            // Decode upgrades
            $upgradesArray =
                array_filter($upgradesArray, function($upgrade)
                {
                    list($unlocked, $bought) = str_split($upgrade);
                    return(min($bought, 1)) ? true : false;
                });

            array_walk($upgradesArray, function(&$upgrade, $key) {
                $upgrade = Lang::get("upgrades.$key");
            });


            // Decode achievements
            $achievementsArray = array_filter($achievementsArray);

            array_walk($achievementsArray, function(&$achieve, $key) {
                $achieve = Lang::get("achievements.$key");
            });


            $this->gameData['upgrades']     = $upgradesArray;
            $this->gameData['achievements'] = $achievementsArray;


            // Calculate prestige (a.k.a., Heavenly Chips)
            // This is pretty much a direct PHP rewrite of the CC code. No Math object, kekekeke!
            $prestige = intval(bcdiv($this->gameData['cookies_reset'], '1000000000000'));
            $this->gameData['prestige'] = max(0,floor((-1+pow(1+8*$prestige, 0.5)) / 2));

            Event::fire('cookiesync.savedecoded', array($this));

            if($this->gameData['game_version'])
            {
                return true;
            } else {
                return false;
            }
        } catch (ErrorException $e) {
            Log::error('Failed to parse save with id '.$this->id);
            return false;
        }

    }

    // FIXME: It's wicked dirty to put this functionality here, move it somewhere else
    // TODO: Extract Save->allStats() to the view code where it belongs
    // Basically, this is hacked in to give the list of all stats as seen in the 'Stats'
    // popover in the save list. Bad place for it, I know.
    /**
     * @return string
     */
    public function allStats()
    {
        return $this->gameStat('buildings.cursors') . " Cursors" . "\n" .
               $this->gameStat('buildings.grandmas') . " Grandmas" . "\n" .
               $this->gameStat('buildings.farms') . " Farms" . "\n" .
               $this->gameStat('buildings.factories') . " Factories" . "\n" .
               $this->gameStat('buildings.mines') . " Mines" . "\n" .
               $this->gameStat('buildings.shipments') . " Shipments" . "\n" .
               $this->gameStat('buildings.labs') . " Labs" . "\n" .
               $this->gameStat('buildings.portals') . " Portals" . "\n" .
               $this->gameStat('buildings.time_machines') . " T.M.s" . "\n" .
               $this->gameStat('buildings.condensers') . " Condensers";
    }


    /**
     * Retrieve a game statistic by name -- basically avoids the need to
     * directly reference $this->gameData, keeps future extensibility
     * open.
     *
     * @param $name
     *
     * @return null|mixed
     */
    public function gameStat($name)
    {
        if(!array_key_exists('game_version', $this->gameData))
        {
            $this->decode();
        }

        if(array_key_exists($name, $this->gameData))
        {
            return $this->gameData[$name];
        }
        else
        {
            return null;
        }
    }

    /**
     * @param $value
     * @return string
     */
    private function uncompressLargeBin($value)
    {
        $output = '';

        foreach(explode(';', $value) as $val)
        {
            $output .= $this->uncompressBin($val);
        }

        return $output;
    }

    /**
     * @param $value
     * @return string
     */
    private function uncompressBin($value)
    {
        // Explode, reverse, remove an element from both ends, send back binary string.
        $binary =  decbin($value);
        $binaryArray = str_split($binary);
        $binaryArray = array_reverse($binaryArray);
        array_shift($binaryArray);
        array_pop($binaryArray);
        return implode($binaryArray);
    }


    public function __get($var)
    {
        switch ($var) {
            case "buildings":
            return array(
                'cursors'       => $this->gameStat('buildings.cursors'),
                'grandmas'      => $this->gameStat('buildings.grandmas'),
                'farms'         => $this->gameStat('buildings.farms'),
                'factories'     => $this->gameStat('buildings.factories'),
                'mines'         => $this->gameStat('buildings.mines'),
                'shipments'     => $this->gameStat('buildings.shipments'),
                'labs'          => $this->gameStat('buildings.labs'),
                'portals'       => $this->gameStat('buildings.portals'),
                'time_machines' => $this->gameStat('buildings.time_machines'),
                'condensers'    => $this->gameStat('buildings.condensers'),
            );

            case "achievements":
                return $this->gameStat("achievements");

            case "upgrades":
                return $this->gameStat("upgrades");

            default:
                return parent::__get($var);
        }
    }

//    public function __call($function, $args)
//    {
//
//    }



    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        $this->decode();
        return json_encode(
            array(
                 'user' => $this->user->toJson(),
                 'game' => $this->game->toJson(),
                 'cookies' => $this->cookies(),
                 'data' => $this->save_data,
                 'game_data' => $this->gameData,
                 'all_time_cookies' => $this->allTimeCookies(),
                 'heavenly_chips' => $this->heavenlyChips(),
                 'grandmapocalypse' => $this->isGrandmapocalypse(),
            )
        );
    }
}
