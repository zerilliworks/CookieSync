<?php

use Illuminate\Database\Migrations\Migration;

class UserStats extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('user_stats', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->string('name');
            $table->text('value');
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
		Schema::drop('user_stats');
	}

}


// howdy how now how are you doingwho who who he hah he howedy ho 
