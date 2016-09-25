<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/14/16
 * Time: 2:53 PM
 */

namespace App;

abstract class Random extends GenericModel
{

	function __construct(array $attributes= array())
	{
		parent::__construct($attributes);
	}

	public static function random(){
		return self::inRandomOrder()->first();
	}

}