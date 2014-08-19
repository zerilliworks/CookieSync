<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBakeryNameToSavesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::table('saves', function(Blueprint $table) {
            $table->string('bakery_name')->nullable();
        });

        Artisan::call('migratedata:bakery');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('saves', function (Blueprint $table) {
            $table->dropColumn('bakery_name');
        });
    }

}
