<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/5/16
 * Time: 1:40 PM
 */

namespace App;

abstract class Asset extends GenericModel
{

	private $traitTable;
	private $fillableFromTraitTable;

	function __construct(array $attributes, AssetTrait $traitTable, $fillableFromTraitTable, $callingClassName = self::class)
	{
		$this->traitTable = $traitTable;
		$this->fillableFromTraitTable = $fillableFromTraitTable;
		parent::__construct($attributes, $callingClassName);
	}

	protected function setFillable()
	{
		foreach ($this->fillableFromTraitTable as $type) {
			if(!isset($this[$type])){
				$this[$type] = $this->getTraitRandomByType($type);
			}
		}
	}

	public function getTraitRandomByType($type, $columns=['trait']){
		$trait = $this->traitTable->getRandomByType($type);
		if(count($columns) == 1){
			return $trait[$columns[0]];
		}else{
			$data = [];
			foreach ($columns as $column){
				$data[$column] = $trait[$column];
			}
			return $data;
		}
	}

}