<?php namespace CookieSync\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\Event;
use \User;
use \Save;
use \Game;

class FixGamesCommand extends Command {

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
	 * @return void
	 */
	public function __construct()
	{

		parent::__construct();


	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $newGameCount = 0;
        $fixedSaveCount = 0;

        // Run through all users and correct their saved games
        $allUsers = User::all();
        $userCount = count($allUsers);
        $saveCount = Save::all()->count();
        $brokenSaveCount = Save::whereGameId(null)->count();

        $this->info("Process started with $userCount users.");

        $orphanedSaves = Save::whereUserId(null)->count();
        $this->info("Found $orphanedSaves orphaned saves.");

        $orphanedGames = Game::whereUserId(null)->count();
        $this->info("Found $orphanedGames orphaned games.");

        $orphanedGames = Save::whereSaveData(null)->count();
        $this->info("Found $orphanedGames saves with no data.");

        $this->info("Found $brokenSaveCount saves with no associated Game group.");


        if(!$brokenSaveCount)
        {
            $this->error("No saved games need fixing.");
            exit;
        }


        $this->info("There are $saveCount saves, $brokenSaveCount of which need fixing.");




        // Error counters
        $emptySaves = 0;
        $datelessSaves = 0;
        $fixedDatelessSaves = 0;



        $this->error("MAKE A BACKUP BEFORE PROCEEDING.");
        if($this->confirm("Shall we continue? [y|N]", false))
        {
            $this->info("Starting...");

            $this->info("Validating save data...");

            foreach (Save::all() as $save) {

                try
                {
                    if($save->save_data == null)
                    {
                        $emptySaves++;
                        $save->delete();
                        throw new \Exception("Save with ID $save->id has no data.");

                    }

                    if($save->user->exists)
                    {
                        $orphanedSaves++;
                        $save->delete();
                        throw new \Exception("Save with ID $save->id is orphaned.");
                    }

                    if($save->saved_at == null ||  $save->started_at == null)
                    {
                        $datelessSaves++;

                        try {
                            $save->decode();

                            // Assign actual Date/Time types to the start and save dates
                            $save->started_at = $save->gameStat('date_started');
                            $save->saved_at = $save->gameStat('date_saved');

                            $save->save();
                            $fixedDatelessSaves++;
                        }
                        catch (\Exception $e)
                        {
                            throw new \Exception("Save with ID $save->id is corrupted.");
                        }
                    }

                    if($save->game_id == null)
                    {
                        try
                        {
                            if(!$thisGame = Game::whereDateStarted($save->started_at)->first())
                            {
                                $game = new Game(array(
                                                      'user_id' => $save->user->id,
                                                      'name' => "Game on " . with(new \Carbon\Carbon($save->started_at))->toFormattedDateString(),
                                                      'date_started' => $save->started_at,
                                                      'date_saved' => time(),
                                                      'cookie_history' => ''
                                                 ));
                                $game->save();
                                $save->game_id = $game->id;
                                $save->save();
                            }
                            else
                            {
                                $save->game_id = $thisGame->id;
                                $save->save();
                            }
                        }
                        catch(\Exception $e)
                        {
                            $this->errors[] = $e->getMessage();
                            echo PHP_EOL;
                        }

                        echo ".";

                    }




                } catch (\Exception $e) {
                    $this->errors[] = $e->getMessage();
                }

            }

            echo PHP_EOL;
            $this->info("Done!");
            $this->comment("Found $emptySaves saves without data and deleted them.");
            $this->comment("Added timestamps to $fixedDatelessSaves saves.");

            $this->error("There were ". count($this->errors) . " errors.");

            if(count($this->errors) >= 1 && count($this->errors) < 10)
            {
                foreach($this->errors as $error)
                {
                    $this->error($error);
                }
            }
            else if (count($this->errors) > 10 &&  $this->confirm("Show all errors? [Y|n]", true))
            {
                foreach($this->errors as $error)
                {
                    $this->error($error);
                }
            }
        }
        else
        {
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
        return array(
        );
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
