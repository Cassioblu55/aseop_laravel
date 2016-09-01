<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Services\DBUtils;

class AddNullsToRemainingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dungeon_traits', function (Blueprint $table) {
            $table->string('type')->nullable()->change();
	        $table->string('trait')->nullable()->change();

	        $table->integer('weight')->default(1)->nullable()->change();

	        $table->text('description')->nullable()->change();
        });

	    Schema::table('dungeons', function (Blueprint $table) {
		    $table->string('creator')->nullable()->change();

		    $table->text('purpose')->nullable()->change();
		    $table->text('history')->nullable()->change();
		    $table->text('location')->nullable()->change();
		    $table->text('traps')->nullable()->change();
		    $table->text('other_information')->nullable()->change();
	    });

	    Schema::table('non_player_character_traits', function (Blueprint $table) {
		    $table->string('type')->nullable()->change();
		    $table->string('trait')->nullable()->change();
	    });

	    Schema::table('settlement_traits', function (Blueprint $table) {
		    $table->string('type')->nullable()->change();
		    $table->string('trait')->nullable()->change();
	    });

	    Schema::table('settlements', function (Blueprint $table) {
		    $table->string('race_relations')->nullable()->change();;

		    $table->text('known_for')->nullable()->change();
		    $table->text('notable_traits')->nullable()->change();
		    $table->text('ruler_status')->nullable()->change();
		    $table->text('current_calamity')->nullable()->change();
		    $table->text('other_information')->nullable()->change();

		    $table->integer('population')->default(0)->nullable()->change();

		    DBUtils::addNullable('settlements', 'size', 'CHAR(1)');
	    });

	    Schema::table('traps', function (Blueprint $table) {

		    $table->text('description')->nullable()->change();
		    $table->text('rolls')->nullable()->change();

		    $table->integer('weight')->default(1)->nullable()->change();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('dungeon_traits', function (Blueprint $table) {
		    $table->string('type')->nullable(false)->change();
		    $table->string('trait')->nullable(false)->change();

		    $table->integer('weight')->nullable(false)->change();
		    DBUtils::removeDefault('dungeon_traits', 'weight');

		    $table->text('description')->nullable(false)->change();
	    });

	    Schema::table('dungeons', function (Blueprint $table) {
		    $table->string('creator')->nullable(false)->change();

		    $table->text('purpose')->nullable(false)->change();
		    $table->text('history')->nullable(false)->change();
		    $table->text('location')->nullable(false)->change();
		    $table->text('traps')->nullable(false)->change();
		    $table->text('other_information')->nullable(false)->change();
	    });

	    Schema::table('non_player_character_traits', function (Blueprint $table) {
		    $table->string('type')->nullable(false)->change();
		    $table->string('trait')->nullable(false)->change();
	    });

	    Schema::table('settlement_traits', function (Blueprint $table) {
		    $table->string('type')->nullable(false)->change();
		    $table->string('trait')->nullable(false)->change();
	    });

	    Schema::table('settlements', function (Blueprint $table) {
		    $table->string('race_relations')->nullable(false)->change();;

		    $table->text('known_for')->nullable(false)->change();
		    $table->text('notable_traits')->nullable(false)->change();
		    $table->text('ruler_status')->nullable(false)->change();
		    $table->text('current_calamity')->nullable(false)->change();
		    $table->text('other_information')->nullable(false)->change();

		    $table->integer('population')->nullable()->change();
		    DBUtils::removeDefault('settlements', 'population');

		    DBUtils::removeNullable('settlements', 'size', 'CHAR(1)');
	    });

	    Schema::table('traps', function (Blueprint $table) {

		    $table->text('description')->nullable(false)->change();
		    $table->text('rolls')->nullable(false)->change();

		    $table->integer('weight')->nullable(false)->change();
		    DBUtils::removeDefault('traps', 'weight');
	    });
    }
}
