<?php

use Illuminate\Database\Migrations\Migration;

class ModSavesAddDateColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('saves', function($table)
        {
            $table->dateTime('saved_at')->nullable();
            $table->dateTime('started_at')->nullable();
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
            $table->dropColumn('saved_at');
            $table->dropColumn('started_at');
        });
	}

}