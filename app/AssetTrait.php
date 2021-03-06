<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/5/16
 * Time: 1:52 PM
 */

namespace App;

abstract class AssetTrait extends GenericModel implements ValidTraitTypes
{

	function __construct(array $attributes, $callingClassName = self::class)
	{
		parent::__construct($attributes, $callingClassName);
	}

	public function getRandomByType($type){
		$results = AssetTrait::where('type', $type);
		if(count($results)){
			return $results->inRandomOrder()->first();
		}
		return '';
	}
}