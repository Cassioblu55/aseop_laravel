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
	        $table->text('known_for')->nullable();
	        $table->text('notable_traits')->nullable();
	        $table->text('ruler_status')->nullable();
	        $table->text('current_calamity')->nullable();
	        $table->integer('population');
	        $table->char('size', 1);
	        $table->string('race_relations')->nullable();
	        $table->text('other_information')->nullable();

	        $table->integer('ruler_id');

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
        Schema::drop('settlements');
    }
}
