<?php

use Illuminate\Database\Migrations\Migration;

class Shares extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('shares', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('save_id')->unsigned();
            $table->string('share_code');

            $table->boolean('active')->default(1);

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
		Schema::drop('shares');
	}

}