<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 10/26/13
// Time: 11:11 PM
// For: CookieSync

use Carbon\Carbon;
use CookieSync\Stat\Income;

/**
 * Class Save
 *
 * The Big One. Cookie Clicker saves are a bit complicated, but essentially are
 * serialized data. This class is a PHP implementation of Cookie Clicker's
 * save writing and importing functions with database persistence.
 *
 * Because the numbers in Cookie Clicker can overflow most integer limits, we use
 * PHP's bcmath extensions to perform math on strings of numbers. It's impossible
 * otherwise: Cookie Clicker totally blows out integers on 32-bit platforms,
 * even unsigned integers on 64-bit platforms. PHP does use the same 64-bit IEEE
 * format floating-point numbers as JavaScript, so a single value could hold as
 * many cookies as CookieSync does. However, since we'll be computing
 * statistics on large batches of save data, it's better to use the arbitrary-
 * precision math to avoid rounding errors and general floating-point foolishness.
 *
 * It is not possible to collect enough cookies to break CookieSync. You'll overrun
 * the 64-bit floats a hundred billion times before you do that.
 */

class Save extends Eloquent implements \Illuminate\Support\Contracts\JsonableInterface {

    use \CookieSync\Traits\BigNumberHandler;

    protected $fillable = array('save_data');
    protected $softDelete = true;
    public $gameData = array();
    public $decodedData = '';
    public $rawDataChunks = array();

    protected $caching = true;

    /**
     *  Set up model observers and events
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
//            $model->noCache();
            // Assign actual Date/Time types to the start and save dates
            $model->started_at = $model->gameStat('date_started');
            $model->saved_at = $model->gameStat('date_saved');

            // Assign this save to a game if it doesn't already have one
            if (!$model->game_id) {
                // Find the appropriate game...
                $user = User::find($model->user_id);
                $game = $user->games()->whereDateStarted($model->started_at)->first();

                if (!$game) {
                    $game =
                        $user
                            ->games()
                            ->save(new Game(
                                       [
                                           'name'           => "Game on " . $model->started_at->toFormattedDateString(),
                                           'date_started'   => $model->started_at,
                                           'date_saved'     => with(new Carbon())->toDateTimeString(),
                                           'cookie_history' => ''
                                       ]
                                   )
                            );
                    Event::fire('cookiesync.newgame', array(&$game));

                }
                $model->game_id = $game->id;

            }

            Event::fire('cookiesync.newsave', array($model));
        });

        static::created(function($model)
        {
            // Cache the new data
            $model->pleaseCache()->decode();
        });

        static::deleting(function ($model) {

            Event::fire('cookiesync.savedeleted', array($model));

            // Find out if this is the last save remaining in a game and
            // delete that game if it is.
            $saveCount = $model->game->saves()->count();

            if ($saveCount <= 1) {
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
        return ($this->is_shared == 0) ? false : true;
    }

    /**
     * Create a public link for a save. If it is already public, return the
     * existing shared link.
     *
     * @return bool|SharedSave
     */
    public function makePublic()
    {
        if ($this->isShared()) {
            // It's already shared, so don't repeat the process
            // just return the current shared instance.
            return $this->sharedInstance;
        }

        $shr          = new SharedSave();
        $shr->save_id = $this->id;
        $this->sharedInstance()->save($shr);
        if ($this->save()) {
            $this->is_shared = 1;
            $this->save();

            Event::fire('cookiesync.saveshared', $this);

            return $shr;
        }
        else {
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
        if (!$this->isShared()) {
            // It's not shared, so forget about it.
            return false;
        }


        if ($this->sharedInstance->delete()) {
            $this->is_shared = 0;
            $this->save();

            Event::fire('cookiesync.saveprivatized', array($this));

            return true;

        }
        else {
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
    public function getDataAttribute()
    {
        return $this->attributes['save_data'];
    }


    /**
     * Get the building incomes in a convenient array
     *
     * @return array
     */
    public function getBuildingIncomeAttribute()
    {
        return array(
          'cursors'       => $this->gameStat('buildings.cursors.production'),
          'grandmas'      => $this->gameStat('buildings.grandmas.production'),
          'farms'         => $this->gameStat('buildings.farms.production'),
          'factories'     => $this->gameStat('buildings.factories.production'),
          'mines'         => $this->gameStat('buildings.mines.production'),
          'shipments'     => $this->gameStat('buildings.shipments.production'),
          'labs'          => $this->gameStat('buildings.labs.production'),
          'portals'       => $this->gameStat('buildings.portals.production'),
          'time_machines' => $this->gameStat('buildings.time_machines.production'),
          'condensers'    => $this->gameStat('buildings.condensers.production'),
          'prisms'        => $this->gameStat('buildings.prisms.production'),
        );
    }


    /**
     * Get the building expenses in a convenient array
     *
     * @return array
     */
    public function getBuildingsExpenseAttribute()
    {
        return array(
          'cursors'       => $this->gameStat('buildings.cursors.expense'),
          'grandmas'      => $this->gameStat('buildings.grandmas.expense'),
          'farms'         => $this->gameStat('buildings.farms.expense'),
          'factories'     => $this->gameStat('buildings.factories.expense'),
          'mines'         => $this->gameStat('buildings.mines.expense'),
          'shipments'     => $this->gameStat('buildings.shipments.expense'),
          'labs'          => $this->gameStat('buildings.labs.expense'),
          'portals'       => $this->gameStat('buildings.portals.expense'),
          'time_machines' => $this->gameStat('buildings.time_machines.expense'),
          'condensers'    => $this->gameStat('buildings.condensers.expense'),
          'prisms'        => $this->gameStat('buildings.prisms.expense'),
        );
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
     *
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
     */
    public function isGrandmapocalypse()
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
     * @throws CookieSync\Errors\DecodingFailedException
     * @return Save $this
     */
    public function decode()
    {
        if(array_key_exists('game_version',$this->gameData)) {
            // It's already decoded, don't do it again.
            return $this;
        }

        if($this->caching && Cache::has("saves:$this->id:gamedata"))
        {
            $this->gameData = Cache::get("saves:$this->id:gamedata");
            $this->gameData['date_saved'] = Carbon::parse($this->gameData['date_saved']);
            $this->gameData['date_started'] = Carbon::parse($this->gameData['date_started']);
            $this->gameData['bakery_epoch'] = Carbon::parse($this->gameData['bakery_epoch']);
            return $this;
        }

        $data = explode('|', $decodedData = base64_decode($this->data));
        /**
         *  After exploding, the game data is broken up like this:
         *
         *  array[0] => game version
         *
         *  array[1] => null
         *
         *  array[2] => game dates : semicolon-delimited list of timestamps
         *           ---
         *           => [0 => startDate, 1 => fullDate, 2 => lastDate, 3 => bakeryName]
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

        $this->decodedData   = $decodedData;
        $this->rawDataChunks = $data;

        $saveStats        = explode(';', $data[2]);
        $cookieStats  = explode(';', $data[4]);
        $upgrades     = $this->uncompressLargeBin($data[6]);
        $achievements = $this->uncompressLargeBin($data[7]);

        $this->gameData['game_version']                 = $data[0];

        $this->gameData['date_started']                 = Carbon::createFromTimestamp(substr($saveStats[0], 0, -3));    // Substr required to trim milliseconds that appear in JS timestamps
        $this->gameData['date_saved']                   = Carbon::createFromTimestamp(substr($saveStats[2], 0, -3));

        if(is_numeric($saveStats[1])) {
            $this->gameData['bakery_epoch']             = Carbon::createFromTimestamp(substr($saveStats[1], 0, -3));
        } else {
            $this->gameData['bakery_epoch']             = $this->gameData['date_started'];
        }

        $this->gameData['bakery_name']                  = isset($saveStats[3]) ? $saveStats[3] : null;
        $this->gameData['banked_cookies']               = $this->expandScientific($cookieStats[0]);
        $this->gameData['raw_banked_cookies']           = $cookieStats[0];
        $this->gameData['alltime_cookies']              = $this->expandScientific($cookieStats[1]);
        $this->gameData['raw_alltime_cookies']          = $cookieStats[1];
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
        $prisms       = explode(',', $buildings[10]);

        $this->gameData['buildings']['cursors']       = $cursors[0];
        $this->gameData['buildings']['grandmas']      = $grandmas[0];
        $this->gameData['buildings']['farms']         = $farms[0];
        $this->gameData['buildings']['factories']     = $factories[0];
        $this->gameData['buildings']['mines']         = $mines[0];
        $this->gameData['buildings']['shipments']     = $shipments[0];
        $this->gameData['buildings']['labs']          = $alchemyLabs[0];
        $this->gameData['buildings']['portals']       = $portals[0];
        $this->gameData['buildings']['time_machines'] = $timeMachines[0];
        $this->gameData['buildings']['condensers']    = $condensers[0];
        $this->gameData['buildings']['prisms']        = $prisms[0] ? $prisms[0] : 0;

        $this->gameData['building_production']['cursors']       = $cursors[2];
        $this->gameData['building_production']['grandmas']      = $grandmas[2];
        $this->gameData['building_production']['farms']         = $farms[2];
        $this->gameData['building_production']['factories']     = $factories[2];
        $this->gameData['building_production']['mines']         = $mines[2];
        $this->gameData['building_production']['shipments']     = $shipments[2];
        $this->gameData['building_production']['labs']          = $alchemyLabs[2];
        $this->gameData['building_production']['portals']       = $portals[2];
        $this->gameData['building_production']['time_machines'] = $timeMachines[2];
        $this->gameData['building_production']['condensers']    = $condensers[2];
        $this->gameData['building_production']['prisms']        = isset($prisms[2]) ? $prisms[2] : 0;

        $this->gameData['buildings_income'] = array_reduce($this->gameData['building_production'], function($total, $building){
            return bcadd($total, $building);
        }, 0);


        $this->gameData['building_expense']['cursors']       = Income::spentOnBuilding('cursor', $this->gameData['buildings']['cursors']);
        $this->gameData['building_expense']['grandmas']      = Income::spentOnBuilding('grandma', $this->gameData['buildings']['grandmas']);
        $this->gameData['building_expense']['farms']         = Income::spentOnBuilding('farm', $this->gameData['buildings']['farms']);
        $this->gameData['building_expense']['factories']     = Income::spentOnBuilding('factory', $this->gameData['buildings']['factories']);
        $this->gameData['building_expense']['mines']         = Income::spentOnBuilding('mine', $this->gameData['buildings']['mines']);
        $this->gameData['building_expense']['shipments']     = Income::spentOnBuilding('shipment', $this->gameData['buildings']['shipments']);
        $this->gameData['building_expense']['labs']          = Income::spentOnBuilding('lab', $this->gameData['buildings']['labs']);
        $this->gameData['building_expense']['portals']       = Income::spentOnBuilding('portal', $this->gameData['buildings']['portals']);
        $this->gameData['building_expense']['time_machines'] = Income::spentOnBuilding('time_machine', $this->gameData['buildings']['time_machines']);
        $this->gameData['building_expense']['condensers']    = Income::spentOnBuilding('condenser', $this->gameData['buildings']['condensers']);
        $this->gameData['building_expense']['prisms']        = Income::spentOnBuilding('prism', $this->gameData['buildings']['prisms']);

        $buildingExpense = '0';

        foreach($this->gameData['building_expense'] as $expense)
        {
            $buildingExpense = bcadd($expense, $buildingExpense);
        }

        $this->gameData['total_buildings_expense'] = $buildingExpense;


        $this->gameData['building_count'] = array_reduce($this->buildings, function($count, $item)
        {
            return $count + intval($item);
        }, 0);

        $this->gameData['upgrades.binary']     = $upgrades;
        $this->gameData['achievements.binary'] = $achievements;
        $this->gameData['upgrades.raw']        = $data[6];
        $this->gameData['achievements.raw']    = $data[7];


        // Split upgrades into pairs of bits, split achievements into single bits
        // Upgrades are [bool, bool] which is [unlocked?, bought?]
        $upgradesArray     = str_split($upgrades, 2);
        $achievementsArray = str_split($achievements);


        // Decode upgrades
        $upgradesArray =
            array_filter($upgradesArray, function ($upgrade) {
                list($unlocked, $bought) = str_split($upgrade);

                return (min($bought, 1)) ? true : false;
            });

        $this->gameData['upgrades']     = array_keys($upgradesArray);
        $this->gameData['achievements'] = array_keys(array_filter($achievementsArray));


        // Calculate prestige (a.k.a., Heavenly Chips)
        // This is pretty much a direct PHP rewrite of the CC code. No Math object, kekekeke!
        $prestige                   = floatval(bcdiv($this->expandScientific($this->gameData['cookies_reset']), '1000000000000'));
        $this->gameData['prestige'] = max(0, floor((-1 + pow(1 + 8 * $prestige, 0.5)) / 2));

        Event::fire('cookiesync.savedecoded', array($this));

        if (array_key_exists('game_version',$this->gameData)) {
            if($this->caching) {
                Cache::put("saves:$this->id:gamedata",
                           array_merge($this->gameData,
                                       [
                                            'date_started' => $this->gameData['date_started']->toDateTimeString(),
                                            'bakery_epoch' => $this->gameData['bakery_epoch']->toDateTimeString(),
                                            'date_saved'   => $this->gameData['date_saved']->toDateTimeString(),
                                       ]
                           ), Carbon::now()->addWeek());
            }
            return $this;
        }
        else {
            throw new \CookieSync\Errors\DecodingFailedException();
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
        return $this->gameStat('buildings')['cursors'] . " Cursors" . "\n" .
               $this->gameStat('buildings')['grandmas'] . " Grandmas" . "\n" .
               $this->gameStat('buildings')['farms'] . " Farms" . "\n" .
               $this->gameStat('buildings')['factories'] . " Factories" . "\n" .
               $this->gameStat('buildings')['mines'] . " Mines" . "\n" .
               $this->gameStat('buildings')['shipments'] . " Shipments" . "\n" .
               $this->gameStat('buildings')['labs'] . " Labs" . "\n" .
               $this->gameStat('buildings')['portals'] . " Portals" . "\n" .
               $this->gameStat('buildings')['time_machines'] . " T.M.s" . "\n" .
               $this->gameStat('buildings')['condensers'] . " Condensers";
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
        $this->decode();

        if (array_key_exists($name, $this->gameData)) {
            return $this->gameData[$name];
        }
        else {
            return null;
        }
    }

    /**
     * @param $value
     *
     * @return string
     */
    private function uncompressLargeBin($value)
    {
        $output = '';

        foreach (explode(';', $value) as $val) {
            $output .= $this->uncompressBin($val);
        }

        return $output;
    }

    /**
     * @param $value
     *
     * @return string
     */
    private function uncompressBin($value)
    {
        // Explode, reverse, remove an element from both ends, send back binary string.
        $binary      = decbin($value);
        $binaryArray = str_split($binary);
        $binaryArray = array_reverse($binaryArray);
        array_shift($binaryArray);
        array_pop($binaryArray);

        return implode($binaryArray);
    }

    /**
     * Don't try to hit the cache for this instance. Sets a flag that disables Cache calls.
     *
     * @return $this
     */
    public function noCache()
    {
        $this->caching = false;
        return $this;
    }


    /**
     * Nevermind, do try to hit the cache. Sets a flag that encourages Cache calls.
     *
     * @return $this
     */
    public function pleaseCache()
    {
        $this->caching = true;
        return $this;
    }


    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param string $var
     * @return mixed
     */
    public function __get($var)
    {
        if (array_key_exists($var, $this->gameData)) {
            return $this->gameStat($var);
        }
        else {
            return parent::__get($var);
        }
    }



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
                'user'             => $this->user->toJson(),
                'game'             => $this->game->id,
                'cookies'          => $this->cookies(),
                'data'             => $this->save_data,
                'game_data'        => $this->gameData,
                'all_time_cookies' => $this->allTimeCookies(),
                'heavenly_chips'   => $this->heavenlyChips(),
                'grandmapocalypse' => $this->isGrandmapocalypse(),
            )
        );
    }
}
