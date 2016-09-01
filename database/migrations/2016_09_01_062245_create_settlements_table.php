<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->increments('id');

	        $table->string('name');
	        $table->text('known_for');
	        $table->text('notable_traits');
	        $table->text('ruler_status');
	        $table->text('current_calamity');
	        $table->integer('population');
	        $table->char('size', 1);
	        $table->string('race_relations');
	        $table->text('other_information');

	        $table->integer('ruler_id');

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
        Schema::drop('settlements');
    }
}
