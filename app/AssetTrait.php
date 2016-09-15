<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/5/16
 * Time: 1:52 PM
 */

namespace App;

class AssetTrait extends GenericModel
{

	public function getRandomByType($type){
		$results = AssetTrait::where('type', $type);
		if(count($results)){
			return $results->inRandomOrder()->first();
		}
		return '';
	}
}