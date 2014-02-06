<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        DB::table('users')->delete();
		Eloquent::unguard();

		$this->call('UserTableSeeder');
	}

}