<?php

use Illuminate\Database\Migrations\Migration;

class Changelog extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('changelog', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('version');
            $table->datetime('release_date');

            $table->text('description');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('changelog');
	}

}