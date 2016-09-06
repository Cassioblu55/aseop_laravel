<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/5/16
 * Time: 1:40 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

abstract class Asset extends Model implements Generate
{

	private $traitTable;
	private $fillableFromTraitTable;

	function __construct(array $attributes, AssetTrait $traitTable, $fillableFromTraitTable)
	{
		$this->traitTable = $traitTable;
		$this->fillableFromTraitTable = $fillableFromTraitTable;
		parent::__construct($attributes);
	}

	protected function setPublic(){
		$this->setIfFeildNotPresent('public', function(){
			return false;
		});
	}

	protected function setIfFeildNotPresent($field, $funct){
		if(!isSet($this[$field])){
			$this[$field] = $funct();
		}
	}

	protected function setFillable()
	{
		foreach ($this->fillableFromTraitTable as $type) {
			if(!isset($this[$type])){
				$this[$type] = $this->getTraitRandomByType($type);
			}
		}
	}

	public function getTraitRandomByType($type){
		return $this->traitTable->getRandomByType($type);
	}

}