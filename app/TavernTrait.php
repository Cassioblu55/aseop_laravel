<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;

class TavernTrait extends AssetTrait implements Upload
{
	protected $guarded = [];

	private $logging;

	const TYPE = 'type';
	const COL_TRAIT = 'trait';

	const UPLOAD_COLUMNS = [self::COL_TRAIT, self::TYPE];

	protected $rules = [
		self::COL_TRAIT => 'required'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

	function __construct(array $attributes = [])
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
			$tavernTrait = new self();
			$tavernTrait->setUploadValues($row);
			return (isSet($tavernTrait->id));
		};

		$runOnUpdate = function($row){
			$tavernTrait = self::where(self::ID, $row[self::ID])->first();
			$tavernTrait->setUploadValues($row);
			return ($tavernTrait->presentValuesEqual($row));
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
		return Tavern::getAllValidTraitTypes();
	}
}
