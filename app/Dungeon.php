<?php

namespace App;

use App\Services\Utils;
use Illuminate\Support\Facades\Auth;

class Dungeon extends Asset
{
	protected $guarded = [];

	const TRAIT_TABLE = DungeonTrait::class;
	const FILLABLE_FROM_TRAIT_TABLE = ['name', 'purpose', 'history','location', 'creator'];

	const SMALL = "S";
	const MEDIUM = "M";
	const LARGE = "L";
	const VALID_SIZE_OPTIONS = [self::SMALL, self::MEDIUM, self::LARGE];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}


	function __construct(array $attributes= array())
	{
		$class = self::TRAIT_TABLE;
		parent::__construct($attributes,new $class() ,self::FILLABLE_FROM_TRAIT_TABLE);
	}

	public static function generate(){
		$dungeon = new Dungeon();
		$dungeon->setMissing();
		$dungeon['owner_id'] = Auth::user()->id;
		$dungeon['approved'] = false;
		return $dungeon;
	}


	private function setMissing(){
		$this->setIfFeildNotPresent('size', function(){
			return Utils::getRandomFromArray(self::VALID_SIZE_OPTIONS);
		});
		$this->setPublic();
		$this->setFillable();
	}

}
