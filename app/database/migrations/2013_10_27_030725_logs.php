<?php

use Illuminate\Database\Migrations\Migration;

class Logs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('logs', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('event');
            $table->string('type');
            $table->text('details');
            $table->integer('active_user')->unsigned()->nullable();

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
		Schema::drop('logs');
	}

}