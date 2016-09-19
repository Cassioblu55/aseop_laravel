<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/14/16
 * Time: 2:53 PM
 */

namespace app;

use App\GenericModel;

class Random extends GenericModel
{

	public static function random(){
		return self::inRandomOrder()->first();
	}

}