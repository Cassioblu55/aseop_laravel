<?php

namespace App;

use App\Services\Utils;

class Dungeon extends Asset
{
	protected $guarded = [];

	const TRAIT_TABLE = DungeonTrait::class;
	const FILLABLE_FROM_TRAIT_TABLE = ['name', 'purpose', 'history','location', 'creator'];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

	public function addMissing(){
		$this->size = Utils::getRandomFromArray(['S','M','L']);
		$this->public = $this->public || false;
		$this->setFillable();
	}

	function __construct(array $attributes= array())
	{
		$class = self::TRAIT_TABLE;
		parent::__construct($attributes,new $class() ,self::FILLABLE_FROM_TRAIT_TABLE);
	}



}
