<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Leaderboard extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
    Schema::create('leaderboard', function (Blueprint $table) {
      $table->engine = 'InnoDB';
      $table->increments('id');

      $table->integer('user_id')->unsigned();
      $table->integer('rank')->unsigned();
      $table->double('cookies');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('leaderboard');
	}

}
