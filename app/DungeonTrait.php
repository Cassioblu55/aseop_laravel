<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;
use App\Services\DownloadHelper;
use App\Services\Validate;

class DungeonTrait extends AssetTrait implements Upload 
{

	protected $guarded = [];

	private $logging;

	const TYPE = 'type';
	const COL_TRAIT = 'trait';
	const WEIGHT = 'weight';
	const DESCRIPTION = 'description';

	const UPLOAD_COLUMNS = [self::COL_TRAIT, self::TYPE, self::WEIGHT, self::DESCRIPTION, self::COL_PUBLIC];

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

		$typeValidation = Validate::getInArrayRule(self::getValidTraitTypes(), 'required|max:255');
		$this->addCustomRule(self::TYPE,$typeValidation);

		parent::__construct($attributes);
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$dungeonTrait = new self();
			return $dungeonTrait->setUploadValues($row);
		};

		$runOnUpdate = function($row){
			$dungeonTrait = self::where(self::ID, $row[self::ID])->first();
			if($dungeonTrait==null){
				Logging::error("Could not update, Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			return $dungeonTrait->setUploadValues($row);
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	public function validate($overrideDefaultValidationRules = false)
	{
		return parent::validate($overrideDefaultValidationRules) && !$this->duplicateFound();
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		return $this->runUpdateOrSave();
	}

	public static function getValidTraitTypes()
	{
		return Dungeon::FILLABLE_FROM_TRAIT_TABLE;
	}

	public static function download($fileName)
	{
		return DownloadHelper::getDownloadFile(self::all(),$fileName);
	}

}
