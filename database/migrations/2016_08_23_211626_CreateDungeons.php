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
		    $table->text('purpose');
		    $table->text('history');
		    $table->text('location');
		    $table->string('creator');
		    $table->text('map');
		    $table->text('traps');
		    $table->text('other_information');
		    $table->char('size', 1);
		    $table->integer('owner_id');
		    $table->boolean('approved');
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
	    Schema::drop('dungeons');
    }
}
