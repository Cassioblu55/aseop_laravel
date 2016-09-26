<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;

class Villain extends Asset
{
    protected $guarded = [];

	private $logging;

	const TRAIT_TABLE = VillainTrait::class;
	const FILLABLE_FROM_TRAIT_TABLE = [];

	const TYPE = 'type', KIND = 'kind', DESCRIPTION = 'description';
	const TRAIT_COLUMN_REQUEST = [self::TYPE, self::KIND, self::DESCRIPTION];

	const NPC_ID = 'npc_id', METHOD_TYPE = 'method_type', METHOD_DESCRIPTION = 'method_description', SCHEME_TYPE = 'scheme_type', SCHEME_DESCRIPTION = 'scheme_description', WEAKNESS_TYPE = 'weakness_type', WEAKNESS_DESCRIPTION = 'weakness_description', OTHER_INFORMATION = 'other_information';

	const UPLOAD_COLUMNS = [self::NPC_ID, self::METHOD_DESCRIPTION, self::METHOD_TYPE, self::SCHEME_DESCRIPTION, self::SCHEME_TYPE, self::WEAKNESS_TYPE, self::WEAKNESS_DESCRIPTION, self::OTHER_INFORMATION];

	const WEAKNESS = 'weakness';
	const METHOD = 'method';
	const SCHEME = 'scheme';
	const FILLABLE_TRAIT_ARRAY = [self::WEAKNESS, self::METHOD, self::SCHEME];

	protected $rules = [
		self::METHOD_TYPE => 'required_with:'.self::METHOD_DESCRIPTION,
		self::METHOD_DESCRIPTION => 'required_with:'.self::METHOD_TYPE,
		self::WEAKNESS_DESCRIPTION => 'required_with:'.self::WEAKNESS_TYPE,
		self::WEAKNESS_TYPE => 'required_with:'.self::WEAKNESS_DESCRIPTION,
		self::SCHEME_TYPE => 'required_with:'.self::SCHEME_DESCRIPTION,
		self::SCHEME_DESCRIPTION => 'required_with:'.self::SCHEME_TYPE
	];

    public function user()
    {
        return $this->belongsTo('App\User', self::OWNER_ID);
    }

	public function npc()
	{
		return $this->belongsTo('App\NonPlayerCharacter', self::NPC_ID);
	}

	function __construct(array $attributes= array())
	{
		$this->logging = new Logging(self::class);

		$npcValidation = $this->getUniqueWithIgnoreSelfRule("villains", self::NPC_ID, 'required|integer|exists:non_player_characters,id');
		$this->addCustomRule(self::NPC_ID,$npcValidation);

		$class = self::TRAIT_TABLE;
		parent::__construct($attributes,new $class() ,self::FILLABLE_FROM_TRAIT_TABLE);
	}

    public static function generate()
    {
	    $villain = new Villain();
	    $villain->addMissing();
	    $villain->setRequiredMissing();
	    $villain->save();
	    return $villain;
    }

    private function setNpc(){
    	$this->setIfFeildNotPresent(self::NPC_ID, function(){
		    return NonPlayerCharacter::generate()->id;
	    });
    }

    public function addMissing(){
		$this->setNpc();
	    foreach (self::FILLABLE_TRAIT_ARRAY as $type){
		    $this->setVillainTrait($type);
	    }
    }

    private function setVillainTrait($type){
    	$trait = $this->getTraitRandomByType($type, self::TRAIT_COLUMN_REQUEST);
	    $this[$type."_type"] = $trait[self::KIND];
	    $this[$type."_description"] = $trait[self::DESCRIPTION];
    }

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$villain = new self();
			$villain->setUploadValues($row);
			return (isSet($villain->id));
		};

		$runOnUpdate = function($row){
			$villain = self::where(self::ID, $row[self::ID])->first();
			if($villain==null){
				Logging::error("Could not update, Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			$villain->setUploadValues($row);
			return ($villain->presentValuesEqual($row));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();

		if($this->validate()){
			isSet($this->id) ? $this->update() : $this->save();
		}else{
			$this->logging->logError($this->getErrorMessage());
		}
	}

}