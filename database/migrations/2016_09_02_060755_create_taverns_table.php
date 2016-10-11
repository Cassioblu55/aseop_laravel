<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTavernsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taverns', function (Blueprint $table) {
            $table->increments('id');

	        $table->string('name');
	        $table->string('type') ->nullable();
	        $table->text('other_information')->nullable();
	        $table->integer('tavern_owner_id');

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
        Schema::drop('taverns');
    }
}
