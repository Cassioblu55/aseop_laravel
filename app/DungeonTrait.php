<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;

class DungeonTrait extends AssetTrait implements Upload 
{

	protected $guarded = [];

	private $logging;

	const TYPE = 'type';
	const COL_TRAIT = 'trait';
	const WEIGHT = 'weight';
	const DESCRIPTION = 'description';

	const UPLOAD_COLUMNS = [self::COL_TRAIT, self::TYPE, self::WEIGHT, self::DESCRIPTION];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

	protected $rules = [
		self::COL_TRAIT => 'required',
		self::WEIGHT => 'required|integer|min:0'
	];

	function __construct(array $attributes= array())
	{
		$this->logging = new Logging(self::class);

		$this->addIgnoreWhenLookingForDuplicate(self::WEIGHT);

		$typeValidation = $this->getInArrayRule(self::getValidTraitTypes(), 'required|max:255');
		$this->addCustomRule(self::TYPE,$typeValidation);

		parent::__construct($attributes);
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$dungeonTrait = new self();
			$dungeonTrait->setUploadValues($row);
			return (isSet($dungeonTrait->id));
		};

		$runOnUpdate = function($row){
			$dungeonTrait = self::where(self::ID, $row[self::ID])->first();
			$dungeonTrait->setUploadValues($row);
			return ($dungeonTrait->presentValuesEqual($row));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		if($this->validate() && !$this->duplicateFound()){
			isSet($this->id) ? $this->update() : $this->save();
		}else{
			$this->logging->logError($this->getErrorMessage());
		}
	}

	public static function getValidTraitTypes()
	{
		return Dungeon::FILLABLE_FROM_TRAIT_TABLE;
	}


}
