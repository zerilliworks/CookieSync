<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerifiedColumnToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table) {
            $table->boolean('email_verified')->default(0);
            $table->string('pending_email')->nullable();
            $table->string('verify_hash')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('email_verified');
            $table->dropColumn('verify_hash');
            $table->dropColumn('pending_email');
        });
	}

}
