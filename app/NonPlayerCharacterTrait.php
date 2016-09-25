<?php

namespace App;

use App\Services\AddBatchAssets;
use app\Services\Logging;

class NonPlayerCharacterTrait extends AssetTrait implements Upload
{
	protected $guarded = [];

	private $logging;

	const TYPE = 'type', COL_TRAIT = 'trait';

	const UPLOAD_COLUMNS = [self::COL_TRAIT, self::TYPE];

	public function user()
	{
		return $this->belongsTo('App\User', self::OWNER_ID);
	}

	protected $rules = [
		self::COL_TRAIT => 'required'
	];

	function __construct(array $attributes= array())
	{
		$this->logging = new Logging(self::class);

		$typeValidation = $this->getInArrayRule(self::getValidTraitTypes(), 'required|max:255');
		$this->addCustomRule(self::TYPE,$typeValidation);

		parent::__construct($attributes);
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$npcTrait = new self();
			$npcTrait->setUploadValues($row);
			return (isSet($npcTrait->id));
		};

		$runOnUpdate = function($row){
			$npcTrait = self::where(self::ID, $row[self::ID])->first();
			if($npcTrait==null){
				Logging::log("Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			$npcTrait->setUploadValues($row);
			return ($npcTrait->presentValuesEqual($row));
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
		return NonPlayerCharacter::getAllValidTraitTypes();
	}

}
