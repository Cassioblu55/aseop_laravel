<?php

namespace App;

use App\Services\Logging;
use App\Services\AddBatchAssets;
use App\Services\Validate;

class Tavern extends Asset
{
	private $logging;

	protected $guarded = [self::OWNER_ID, self::APPROVED];

	const TRAIT_TABLE = TavernTrait::class;

	const TYPE = 'type', TAVERN_OWNER_ID = 'tavern_owner_id', NAME = 'name', OTHER_INFORMATION = 'other_information';
	
	const FIRST_NAME = 'first_name', LAST_NAME = 'last_name';
	
	const UPLOAD_COLUMNS = [self::NAME, self::TAVERN_OWNER_ID, self::OTHER_INFORMATION, self::TYPE, self::COL_PUBLIC];

	const FILLABLE_FROM_TRAIT_TABLE = [self::TYPE];

	const ADDITIONAL_FILLABLE_FROM_TRAIT_TABLE = [self::LAST_NAME, self::FIRST_NAME];

	protected $rules = [
		self::NAME => 'required|max:255',
		self::TAVERN_OWNER_ID => 'required|integer|exists:non_player_characters,id',
		self::TYPE => 'required|max:255'
	];

	public function user()
	{
		return $this->belongsTo('App\User', self::OWNER_ID);
	}

	function __construct(array $attributes= array())
	{
		$this->logging = new Logging(self::class);

		$class = self::TRAIT_TABLE;
		parent::__construct($attributes,new $class() ,self::FILLABLE_FROM_TRAIT_TABLE);
	}

	public static function generate(){
		$tavern = new Tavern();
		$tavern->addMissing();
		$tavern->runUpdateOrSave();
		return $tavern;

	}

	public function addMissing(){
		$this->setName();
		$this->setFillable();
		$this->setRequiredMissing();
		$this->setTavernOwner();
	}

	private function setName(){
		$this->setIfFieldNotPresent(self::NAME, function(){
			return $this->getTraitRandomByType(self::FIRST_NAME)." ".$this->getTraitRandomByType(self::LAST_NAME);
		});
	}

	private function setTavernOwner(){
		$this->setIfFieldNotPresent(self::TAVERN_OWNER_ID, function(){
			return $this->createNewTavernOwnerOnlyIfTavernValidOtherwise();
		});
	}

	private function createNewTavernOwnerOnlyIfTavernValidOtherwise(){
		$valid = Validate::validWithIgnoredRule($this->attributesToArray(), $this->rules, self::TAVERN_OWNER_ID);
		return ($valid) ? NonPlayerCharacter::generate()->id : null;
	}

	public function owner()
	{
		return $this->belongsTo('App\NonPlayerCharacter', self::TAVERN_OWNER_ID);
	}

	public static function upload($filePath)
	{
		return self::runUpload($filePath, self::UPLOAD_COLUMNS);
	}

	public static function getNewSelf(){
		return new self();
	}

	public function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		return $this->runUpdateOrSave();
	}

	public static function getAllValidTraitTypes(){
		return array_merge(self::ADDITIONAL_FILLABLE_FROM_TRAIT_TABLE, self::FILLABLE_FROM_TRAIT_TABLE);
	}
}
