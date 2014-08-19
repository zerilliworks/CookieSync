<?php namespace CookieSync\Commands;

use CookieSync\Traits\ModelBatchTrait;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use \User;
use \Save;
use \Game;

class FixGamesCommand extends Command {

    use ModelBatchTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cookiesync:fixgames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan, repair, and upgrade CookieSync data.';

    protected $errors = array();

    /**
     * Create a new command instance.
     *
     * @return \CookieSync\Commands\FixGamesCommand
     */
    public function __construct()
    {

        parent::__construct();

        // Write to our own log file
        Log::useFiles(storage_path() . '/logs/fixgames.log');

    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     * @return mixed
     */
    public function fire()
    {
        // Run through all users and correct their saved games
        $allUsers = User::all();
        $userCount = count($allUsers);
        $saveCount = Save::all()->count();

        $this->info("Process started with $userCount users.");

        // Error counters
        $datelessSaves = 0;
        $irreparableSaves = 0;
        $corruptSaves = 0;

        $orphanedSaves = Save::whereUserId(null)->count();
        $this->info("Found $orphanedSaves orphaned saves.");

        $orphanedGames = Game::whereUserId(null)->count();
        $this->info("Found $orphanedGames orphaned games.");

        $emptySaves = Save::whereSaveData(null)->count();
        $this->info("Found $emptySaves saves with no data.");

        $emptySaves = Save::whereSavedAt(null)->count();
        $this->info("Found $emptySaves saves with no data.");

        $gamelessSaves = Save::whereGameId(null)->count();
        $this->info("Found $gamelessSaves saves with no associated Game group.");

        $epochlessSaves = Save::whereBakeryEpoch(null)->count();
        $this->info("Found $epochlessSaves saves with no epoch date.");

        $namelessSaves = Save::whereBakeryName(null)->count();
        $this->info("Found $namelessSaves saves with no name.");

        $this->info("Fsck-ing game data...");
        $mislabeledGames = 0;
        foreach($this->gameBatch() as $game) {
            $gameName = $game->latestSave()->gameStat('bakery_name');
            if($game->name !== $gameName && !empty($gameName))
            {
                $mislabeledGames++;
            }
        }
        $this->info("Found $mislabeledGames games with an incorrect name.");

        $brokenSaveCount = Save::whereNull('game_id')
                               ->orWhereNull('saved_at')
                               ->orWhereNull('save_data')
                               ->orWhereNull('user_id')
                               ->orWhereNull('bakery_epoch')
                               ->orWhereNull('bakery_name')
                               ->count();

        $this->info("There are $saveCount saves, $brokenSaveCount of which need fixing.");

        if($brokenSaveCount == 0) {
            $this->error('Nothing to fix.');
            return;
        }

        $this->error("MAKE A BACKUP BEFORE PROCEEDING.");
        if ($this->confirm("Shall we continue? [y|N]", false)) {
            $this->info("Starting...");

            $this->info("Validating save data...");

            //
            // CHECK FOR ANY ERRORS IN SAVE DATA
            //

            foreach (Save::all() as $save) {

                /*
                 * Confirm that the data is not corrupt
                */

                if (!$save->decode()) {
                    $corruptSaves++;
                }


                /*
                 * Confirm that the save has proper timestamps
                */

                if ($save->saved_at == null || $save->started_at == null) {
                    $datelessSaves++;
                }


                /*
                 * Confirm that the save is assigned to a game
                */

                if ($save->game_id == null) {
                    $gamelessSaves++;

                }

                if($errorCount > 0)
                {
                    Log::error("Save (id: $save->id) could not be repaired.");
                }
                else
                {
                    Log::info("Save (id: $save->id) was repaired.");
                }

            }






            //
            // FIX ALL ERRORS
            //


            foreach ($this->saveBatch() as $save) {

                $errorCount = 0;

                /*
                 * Confirm that a save has some data
                */

                if ($save->save_data == null) {
                    Log::notice("Save object with ID $save->id has no data.");
                    try {
                        $emptySaves++;
                        $save->delete();
                    }
                    catch (Exception $e) {
                        $errorCount++;
                        Log::error($e->getMessage());
                    }
                }

                /*
                 * Confirm that the data is not corrupt
                */

                try {
                    if (!$save->decode()) {
                        throw new Exception("IRREPARABLE: Save with ID $save->id is corrupt.");
                    }
                }
                catch (Exception $e) {
                    $corruptSaves++;
                    $errorCount++;
                    Log::error($e->getMessage());
                }


                /*
                 * Confirm that the save has proper timestamps
                */

                try {
                    if ($save->saved_at == null || $save->started_at == null) {
                        Log::notice("Save object with ID $save->id has no timestamps.");
                        $datelessSaves++;

                        // Assign actual Date/Time types to the start and save dates
                        $save->started_at = $save->gameStat('date_started');
                        $save->saved_at = $save->gameStat('date_saved');

                        if (!$save->save()) {
                            throw new Exception("IRREPARABLE: Save object with ID $save->id has no timestamps.");
                        }
                    }
                }
                catch (Exception $e) {
                    $errorCount++;
                    Log::error($e->getMessage());
                }


                /*
                 * Confirm that the save is assigned to a game
                */

                if ($save->game_id == null) {
                    Log::notice("Save object with ID $save->id is not assigned to a Game object.");
                    try {
                        if (!$thisGame = Game::whereDateStarted($save->started_at)->first()) {

                            $game = new Game(array(
                                                  'user_id' => $save->user->id,
                                                  'name' =>
                                                  "Game on "
                                                  . with(new \Carbon\Carbon($save->started_at))->toFormattedDateString(),
                                                  'date_started' => $save->started_at,
                                                  'date_saved' => time(),
                                                  'cookie_history' => ''
                                             ));
                            $game->save();
                            $save->game_id = $game->id;
                        }
                        else {
                            $save->game_id = $thisGame->id;
                        }

                        if (!$save->save()) {
                            throw new Exception("IRREPARABLE: Save object with ID $save->id has no timestamps.");
                        }
                    }
                    catch (Exception $e) {
                        Log::error($e->getMessage());
                    }

                    echo ".";

                }

                if($errorCount > 0)
                {
                    Log::error("Save (id: $save->id) could not be repaired.");
                }
                else
                {
                    Log::info("Save (id: $save->id) was repaired.");
                }

            }

            foreach($this->gameBatch() as $game)
            {
                /*
                 * Confirm that the game name matches the latest bakery name
                */

                $gameName = $game->latestSave()->gameStat('bakery_name');
                if($game->name !== $gameName && !empty($gameName))
                {
                    $game->name = $gameName;
                }
            }

            echo PHP_EOL;
            $this->info("Done!");

            $this->error("There were " . count($this->errors) . " errors.");
            $this->comment('You can find a log of all changes and errors in ' . storage_path() . '/logs/fixgames.log');

        }
        else {
            $this->error("Aborted.");
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('pretend', null, InputOption::VALUE_OPTIONAL, 'Simulate changes but do not commit them.', false),
        );
    }

}