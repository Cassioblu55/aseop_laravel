<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveNullableFromTraitTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('non_player_character_traits', function (Blueprint $table) {
			$table->string('type')->nullable(false)->change();
		    $table->string('trait')->nullable(false)->change();
	    });

	    Schema::table('settlement_traits', function (Blueprint $table) {
		    $table->string('type')->nullable(false)->change();
		    $table->string('trait')->nullable(false)->change();
	    });

	    Schema::table('dungeon_traits', function (Blueprint $table) {
		    $table->string('type')->nullable(false)->change();
		    $table->string('trait')->nullable(false)->change();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('non_player_character_traits', function (Blueprint $table) {
		    $table->string('type')->nullable()->change();
		    $table->string('trait')->nullable()->change();
	    });

	    Schema::table('dungeon_traits', function (Blueprint $table) {
		    $table->string('type')->nullable()->change();
		    $table->string('trait')->nullable()->change();
	    });

	    Schema::table('settlement_traits', function (Blueprint $table) {
		    $table->string('type')->nullable()->change();
		    $table->string('trait')->nullable()->change();
	    });
    }
}
