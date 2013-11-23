<?php

use Illuminate\Database\Migrations\Migration;

class ModSavesAddGameColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('saves', function($table)
        {
            $table->integer('game_id')->unsigned()->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('saves', function($table)
        {
            $table->dropColumn('game_id');
        });
	}

}