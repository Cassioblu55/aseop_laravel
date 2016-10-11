<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVillainTraitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villain_traits', function (Blueprint $table) {
            $table->increments('id');

	        $table->text('type')->nullable();
	        $table->text('kind')->nullable();
	        $table->text('description')->nullable();

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
        Schema::drop('villain_traits');
    }
}
