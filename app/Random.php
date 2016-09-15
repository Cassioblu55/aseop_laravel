<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/14/16
 * Time: 2:53 PM
 */

namespace app;


use Illuminate\Database\Eloquent\Model;

class Random extends Model
{

	public static function random(){
		return self::inRandomOrder()->first();
	}

}