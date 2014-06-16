<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCookieStatsToSavesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('saves', function(Blueprint $table)
        {
            $table->double('cookies_banked')->nullable();
            $table->double('cookies_accrued')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('saves', function(Blueprint $table)
        {
            $table->dropColumn('cookies_banked');
            $table->dropColumn('cookies_accrued');
        });
	}

}
