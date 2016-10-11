<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDungeons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('dungeons', function (Blueprint $table) {
		    $table->increments('id');
		    $table->string('name');
		    $table->text('purpose')->nullable();
		    $table->text('history')->nullable();
		    $table->text('location')->nullable();
		    $table->string('creator')->nullable();
		    $table->text('map');
		    $table->text('traps');
		    $table->text('other_information')->nullable();
		    $table->char('size', 1);

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
	    Schema::drop('dungeons');
    }
}
