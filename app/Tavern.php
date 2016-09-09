<?php

namespace App;

use Illuminate\Support\Facades\Auth;

class Tavern extends Asset
{
	protected $guarded = [];

	const TRAIT_TABLE = TavernTrait::class;
	const FILLABLE_FROM_TRAIT_TABLE = ['type'];

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
		$tavern = new Tavern();
		$tavern->addMissing();
		$tavern['owner_id'] = Auth::user()->id;
		$tavern['approved'] = false;
		$tavern->save();
		return $tavern;

	}

	public function addMissing(){
		$this->setName();
		$this->setTavernOwner();
		$this->setFillable();
		$this->setPublic();
	}

	private function setName(){
		$this->setIfFeildNotPresent('name', function(){
			return $this->getTraitRandomByType('first_name')." ".$this->getTraitRandomByType('last_name');
		});
	}

	private function setTavernOwner(){
		$this->setIfFeildNotPresent('tavern_owner_id', function(){
			return NonPlayerCharacter::generate()->id;
		});
	}


	public function owner()
	{
		return $this->belongsTo('App\NonPlayerCharacter', 'tavern_owner_id');
	}
}
