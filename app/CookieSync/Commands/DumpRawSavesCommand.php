<?php namespace Cookiesync\Commands;

use CookieSync\Traits\ModelBatchTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DumpRawSavesCommand extends Command {

    use ModelBatchTrait;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cookiesync:dumprawsaves';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Dump save data to a serialized file';

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
        if(!File::exists($this->argument('file')) || $this->confirm("Overwrite? [y|N]", false))
        {
            $saveCount = \Save::count();
            $current = 0;

            @File::delete($this->argument('file'));
            foreach($this->saveBatch() as $save) {
                $current++;
                File::append($this->argument('file'), preg_replace('/\s+/', '', $save->save_data). $this->option('delimiter'));
                echo "\rWriting $current of $saveCount saves...";
            }
            echo PHP_EOL;
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
			array('file', InputArgument::REQUIRED, 'File to store saves in'),
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
			array('delimiter', null, InputOption::VALUE_OPTIONAL, 'String that will separate the save data', PHP_EOL),
		);
	}

}
