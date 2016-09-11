<?php

namespace App;

use Illuminate\Support\Facades\Auth;

class Villain extends Asset
{
    protected $guarded = [];

	const TRAIT_TABLE = VillainTrait::class;
	const FILLABLE_FROM_TRAIT_TABLE = [];

	const TRAIT_COLUMN_REQUEST = ['type', 'kind', 'description'];

	const WEAKNESS = 'weakness';
	const METHOD = 'method';
	const SCHEME = 'scheme';
	const FILLABLE_TRAIT_ARRAY = [self::WEAKNESS, self::METHOD, self::SCHEME];

    public function user()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

	public function npc()
	{
		return $this->belongsTo('App\NonPlayerCharacter', 'npc_id');
	}

	function __construct(array $attributes= array())
	{
		$class = self::TRAIT_TABLE;
		parent::__construct($attributes,new $class() ,self::FILLABLE_FROM_TRAIT_TABLE);
	}

    public static function generate()
    {
	    $villain = new Villain();
	    $villain->addMissing();
	    $villain['owner_id'] = Auth::user()->id;
	    $villain['approved'] = false;
	    $villain->save();
	    return $villain;
    }

    private function setNpc(){
    	$this->setIfFeildNotPresent('npc_id', function(){
		    return NonPlayerCharacter::generate()->id;
	    });
    }

    public function addMissing(){
		$this->setNpc();
	    foreach (self::FILLABLE_TRAIT_ARRAY as $type){
		    $this->setVillainTrait($type);
	    }
    	$this->setPublic();
    }

    private function setVillainTrait($type){
    	$trait = $this->getTraitRandomByType($type, self::TRAIT_COLUMN_REQUEST);
	    $this[$type."_type"] = $trait['kind'];
	    $this[$type."_description"] = $trait['description'];
    }

}