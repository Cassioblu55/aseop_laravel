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

class Asset extends Model
{

	private $traitTable;
	private $fillableFromTraitTable;

	function __construct(array $attributes, AssetTrait $traitTable, $fillableFromTraitTable)
	{
		$this->traitTable = $traitTable;
		$this->fillableFromTraitTable = $fillableFromTraitTable;
		parent::__construct($attributes);
	}

	protected function setFillable()
	{
		foreach ($this->fillableFromTraitTable as $type) {
			$this[$type] = $this->traitTable->getRandomByType($type);
		}

	}

}