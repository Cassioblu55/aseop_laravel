<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDungeonTraitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dungeon_traits', function (Blueprint $table) {
            $table->increments('id');

			$table->string('type');
	        $table->string('trait');
	        $table->text('description')->nullable();

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
        Schema::drop('dungeon_traits');
    }
}
