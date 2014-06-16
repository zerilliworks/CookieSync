<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 5/22/14
// Time: 1:22 AM
// For: CookieSync


namespace CookieSync\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SharedSave;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use \User;
use \Save;
use \Game;

class CacheRegenCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cookiesync:cache-regen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrub and regenerate the save data cache.';

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
        Log::useFiles(storage_path() . '/logs/cache.log');

    }


    public function fire()
    {
        $this->info("Gathering metadata...");
        $saveCount = Save::count();
        $userCount = User::count();
        $gameCount = Game::count();
        $sharedSaveCount = SharedSave::count();
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
          array('flush', null, InputOption::VALUE_OPTIONAL, 'Completely hose the cache instead of updating keys', true),
        );
    }

} 
