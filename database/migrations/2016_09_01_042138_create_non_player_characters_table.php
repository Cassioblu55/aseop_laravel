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
	        $table->string('last_name')->nullable();
	        $table->integer('age');
	        $table->char('sex', 1)->nullable();
	        $table->integer('height');
	        $table->integer('weight');

	        $table->string('flaw')->nullable();
	        $table->string('interaction')->nullable();
	        $table->string('mannerism')->nullable();
	        $table->string('bond')->nullable();
	        $table->string('appearance')->nullable();
	        $table->string('talent')->nullable();
	        $table->string('ideal')->nullable();
	        $table->string('ability')->nullable();
	        $table->text('other_information')->nullable();

	        $table->integer('owner_id');
	        $table->boolean('approved')->default(false);
	        $table->boolean('public')->default(false);

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
