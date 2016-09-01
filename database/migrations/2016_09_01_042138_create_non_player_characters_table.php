<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNonPlayerCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_player_characters', function (Blueprint $table) {
            $table->increments('id');

	        $table->string('first_name');
	        $table->string('last_name');
	        $table->integer('age');
	        $table->char('sex', 1);
	        $table->integer('height');
	        $table->integer('weight');

	        $table->string('flaw');
	        $table->string('interaction');
	        $table->string('mannerism');
	        $table->string('bond');
	        $table->string('appearance');
	        $table->string('talent');
	        $table->string('ideal');
	        $table->string('ability');

	        $table->integer('owner_id');
	        $table->boolean('approved')->default(false);
	        $table->boolean('public');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('non_player_characters');
    }
}
