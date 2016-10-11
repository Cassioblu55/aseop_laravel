<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVillainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villains', function (Blueprint $table) {
            $table->increments('id');

	        $table->integer('npc_id');
	        $table->string('method_type')->nullable();
	        $table->text('method_description')->nullable();

	        $table->string('scheme_type')->nullable();
	        $table->text('scheme_description')->nullable();

	        $table->string('weakness_type')->nullable();
	        $table->text('weakness_description')->nullable();

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
        Schema::drop('villains');
    }
}
