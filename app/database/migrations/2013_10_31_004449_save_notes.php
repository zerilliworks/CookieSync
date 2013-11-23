<?php

use Illuminate\Database\Migrations\Migration;

class SaveNotes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('save_notes', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('save_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->text('note');

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
		Schema::drop('save_notes');
	}

}