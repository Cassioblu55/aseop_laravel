<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpellsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spells', function (Blueprint $table) {
            $table->increments('id');

	        $table->string('name');
	        $table->string('type');
	        $table->string('class')->nullable();
	        $table->integer('level')->default(0);
	        $table->string('casting_time')->nullable();
	        $table->integer('range')->default(0);
	        $table->string('components')->nullable();
	        $table->string('duration')->nullable();

	        $table->text('description');

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
        Schema::drop('spells');
    }
}
