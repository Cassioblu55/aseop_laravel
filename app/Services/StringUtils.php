<?php

namespace App\Services;

class StringUtils
{
	public static function display($string){
		return ucwords(str_replace("_", " " , $string));
	}

}