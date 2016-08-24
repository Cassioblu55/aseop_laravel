<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class AddDefaultValuesToDungeons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    DB::statement("ALTER TABLE `dungeons` CHANGE COLUMN `approved` `approved` INT(1) NOT NULL DEFAULT 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    DB::statement("ALTER TABLE `dungeons` CHANGE COLUMN `approved` `approved` INT(1) NOT NULL;");
    }
}
