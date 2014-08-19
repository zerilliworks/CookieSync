<?php

namespace CookieSync\Commands;

use CookieSync\Traits\ModelBatchTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MigrateBakeryEpochCommand extends Command {
    use ModelBatchTrait;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'migratedata:bakery';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fill in missing data for bakeries.';

    /**
     * Create a new command instance.
     *
     * @return \CookieSync\Commands\MigrateBakeryEpochCommand
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
        $noEpochCount = \Save::whereNull('bakery_epoch')->count();
        $noNameCount = \Save::whereNull('bakery_name')->count();
        $saveCount = \Save::whereNull('bakery_name')->orWhereNull('bakery_epoch')->count();
        $this->info("$noEpochCount entries are missing an epoch.");
        $this->info("$noNameCount entries are missing a name.");
        $this->info("$saveCount entries need repair.");
        $i = 0;
        foreach($this->saveBatch() as $save) {
            $save->noCache()->decode();
            if(empty($save->bakery_epoch)) {
                $save->bakery_epoch = $save->gameStat('bakery_epoch');
            }

            if(empty($save->bakery_name)) {
                $save->bakery_name = $save->gameStat('bakery_name');
            }

            $save->save();

            if (empty($save->bakery_name) || empty($save->bakery_epoch)) {
                $i++;
            }
            echo "\rMigrated $i out of $saveCount saves...";
        }
        echo PHP_EOL;
        $this->info('Done.');
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
		return array();
	}

}
