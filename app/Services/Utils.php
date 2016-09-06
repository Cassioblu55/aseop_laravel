<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/5/16
 * Time: 2:31 PM
 */

namespace App\Services;


class Utils
{

	public static function getRandomFromArray($array){
		return $array[rand(0, count($array)-1)];
	}

}