<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/5/16
 * Time: 1:52 PM
 */

namespace App;
use Illuminate\Database\Eloquent\Model;

class AssetTrait extends Model
{

	public function getRandomByType($type){
		$results = AssetTrait::where('type', $type);
		if(count($results)){
			return $results->inRandomOrder()->first()['trait'];
		}
		return '';
	}

}