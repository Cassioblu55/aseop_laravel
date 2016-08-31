<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetTrapAprrovedDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		DB::statement("ALTER TABLE `traps` CHANGE COLUMN `approved` `approved` INT(1) NOT NULL DEFAULT 0;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `traps` CHANGE COLUMN `approved` `approved` INT(1) NOT NULL;");
	}
}
