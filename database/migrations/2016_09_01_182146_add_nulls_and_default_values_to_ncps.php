<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Services\DBUtils;

class AddNullsAndDefaultValuesToNcps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('non_player_characters', function (Blueprint $table) {
            $table->integer('age')->nullable()->default(0)->change();
	        $table->integer('height')->nullable()->default(0)->change();
	        $table->integer('weight')->nullable()->default(0)->change();
	        $table->string('last_name')->nullable()->change();
	        $table->string('flaw')->nullable()->change();
	        $table->string('interaction')->nullable()->change();
	        $table->string('mannerism')->nullable()->change();
	        $table->string('bond')->nullable()->change();
	        $table->string('appearance')->nullable()->change();
	        $table->string('talent')->nullable()->change();
	        $table->string('ideal')->nullable()->change();
	        $table->string('ability')->nullable()->change();

	        DBUtils::addNullable('non_player_characters', 'sex', 'char(1)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('non_player_characters', function (Blueprint $table) {
	        $table->integer('age')->nullable(false)->change();
	        $table->integer('height')->nullable(false)->change();
	        $table->integer('weight')->nullable(false)->change();
	        $table->string('last_name')->nullable(false)->change();
	        $table->string('flaw')->nullable(false)->change();
	        $table->string('interaction')->nullable(false)->change();
	        $table->string('mannerism')->nullable(false)->change();
	        $table->string('bond')->nullable(false)->change();
	        $table->string('appearance')->nullable(false)->change();
	        $table->string('talent')->nullable(false)->change();
	        $table->string('ideal')->nullable(false)->change();
	        $table->string('ability')->nullable(false)->change();
	        DBUtils::removeNullable('non_player_characters', 'sex', 'char(1)');


	        $tableName = "non_player_characters";
	        DBUtils::removeDefault($tableName, 'age');
	        DBUtils::removeDefault($tableName, 'height');
	        DBUtils::removeDefault($tableName, 'weight');
        });
    }
}
