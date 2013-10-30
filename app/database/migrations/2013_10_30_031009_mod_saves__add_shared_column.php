<?php

use Illuminate\Database\Migrations\Migration;

class ModSavesAddSharedColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('saves', function($table)
        {
            $table->boolean('is_shared')->default(0);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('users', function($table)
        {
            $table->dropColumn('is_shared');
        });
	}

}