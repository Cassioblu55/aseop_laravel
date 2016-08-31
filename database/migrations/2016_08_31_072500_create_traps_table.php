<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traps', function (Blueprint $table) {
            $table->increments('id');
	        $table->string('name');
	        $table->text('description');
	        $table->text('rolls');
	        $table->integer('weight');

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
        Schema::drop('traps');
    }
}
