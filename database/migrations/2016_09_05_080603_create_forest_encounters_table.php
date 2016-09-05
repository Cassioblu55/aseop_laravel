<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForestEncountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forest_encounters', function (Blueprint $table) {
            $table->increments('id');

	        $table->string('title');
	        $table->text('description')->nullable();
	        $table->text('rolls')->nullable();

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
        Schema::drop('forest_encounters');
    }
}
