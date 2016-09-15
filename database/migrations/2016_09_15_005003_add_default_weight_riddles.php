<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultWeightRiddles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('riddles', function (Blueprint $table) {
	        $table->integer('weight')->nullable(false)->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('riddles', function (Blueprint $table) {
	        $table->integer('weight')->nullable(false)->change();

        });
    }
}
