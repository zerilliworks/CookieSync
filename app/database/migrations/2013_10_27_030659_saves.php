<?php

use Illuminate\Database\Migrations\Migration;

class Saves extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('saves', function ($table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');

            $table->integer('user_id')->unsigned();

            $table->text('save_data');

            $table->timestamps();
            $table->softDeletes();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('saves');
	}

}