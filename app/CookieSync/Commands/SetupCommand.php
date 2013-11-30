<?php namespace CookieSync\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SetupCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cookiesync:setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Do everything necessary to run CookieSync on your own.';

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
		/*
		 * Whup up an SQLite DB for personal use.
		 */

        if($db = new \PDO('sqlite:/' . app_path() . '/database/cookiesync.sqlite'))
        {
            $this->info('Set up SQLite database at ' . app_path() . '/database/cookiesync.sqlite');
        }
        else
        {
            $this->error('Database could not be created.');
            exit;
        }

        $this->info('Running migrations...');
        $this->call('migrate', array('--env' => 'local'));

        $this->info('Publishing configs...');
        $this->call('config:publish', array('package' => 'loic-sharma/profiler'));

        $this->info('Generating application key...');
        $this->call('key:generate');

        $this->info('All set. From now on, use php artisan serve to get going.');

        $this->call('serve');

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
		);
	}

}
