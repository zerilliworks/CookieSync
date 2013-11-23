<?php

use Illuminate\Database\Migrations\Migration;

class Games extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('games', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned();

            $table->string('name');
            $table->dateTime('date_started');
            $table->dateTime('date_saved');

            $table->text('cookie_history');

            $table->softDeletes();
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
		Schema::drop('games');
	}

}