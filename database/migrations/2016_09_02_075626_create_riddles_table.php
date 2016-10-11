<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiddlesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('riddles', function (Blueprint $table) {
			$table->increments('id');

			$table->string('name');
			$table->text('riddle');
			$table->text('solution');
			$table->text('hint')->nullable();
			$table->text('other_information')->nullable();

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
		Schema::drop('riddles');
	}
}