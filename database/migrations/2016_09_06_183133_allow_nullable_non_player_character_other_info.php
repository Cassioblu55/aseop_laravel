<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowNullableNonPlayerCharacterOtherInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('non_player_characters', function (Blueprint $table) {
	        $table->text('other_information')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('non_player_characters', function (Blueprint $table) {
	        $table->text('other_information')->nullable(false)->change();
        });
    }
}
