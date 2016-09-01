<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/1/16
 * Time: 1:35 PM
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DBUtils
{
	const QUOTE_CHARACTER = "`";

	public static function removeDefault($table, $column){
		$statement = 'ALTER TABLE '.self::wrapInQuotes($table).' ALTER '.self::wrapInQuotes($column).' DROP DEFAULT;';
		self::runStatement($statement);
	}

	public static function removeNullable($table, $column, $type){
		$statement = 'ALTER TABLE '.self::wrapInQuotes($table).' MODIFY '.self::wrapInQuotes($column).' '.$type.' NOT NULL;';
		self::runStatement($statement);
	}

	public static function addNullable($table, $column, $type){
		$statement = 'ALTER TABLE '.self::wrapInQuotes($table).' MODIFY '.self::wrapInQuotes($column).' '.$type.' NULL;';
		self::runStatement($statement);
	}


	private static function runStatement($statement){
		DB::statement($statement);
	}

	private static function wrapInQuotes($string){
		return self::QUOTE_CHARACTER.$string.self::QUOTE_CHARACTER;
	}

}