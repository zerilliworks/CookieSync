<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBakeryEpochToSavesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('saves', function(Blueprint $table) {
            $table->dateTime('bakery_epoch')->nullable();
        });

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('saves', function (Blueprint $table) {
            $table->dropColumn('bakery_epoch');
        });
	}

}
