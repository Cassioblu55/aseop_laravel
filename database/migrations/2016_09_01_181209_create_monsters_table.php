<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonstersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monsters', function (Blueprint $table) {
            $table->increments('id');

	        $table->string('name');
	        $table->string('hit_points')->nullable();
	        $table->string('stats')->nullable();
	        $table->text('skills')->nullable();
	        $table->text('languages')->nullable();
	        $table->float('challenge')->nullable();
	        $table->text('abilities')->nullable();
	        $table->text('actions')->nullable();
	        $table->text('found')->nullable();
	        $table->text('description')->nullable();

	        $table->integer('speed')->nullable()->default(0);
	        $table->integer('armor')->nullable()->default(0);
	        $table->integer('xp')->nullable()->default(0);

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
        Schema::drop('monsters');
    }
}
