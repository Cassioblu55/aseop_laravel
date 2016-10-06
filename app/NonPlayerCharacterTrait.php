<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;
use App\Services\DownloadHelper;
use App\Services\Validate;

class NonPlayerCharacterTrait extends AssetTrait implements Upload
{
	protected $guarded = [];

	private $logging;

	const TYPE = 'type', COL_TRAIT = 'trait';

	const UPLOAD_COLUMNS = [self::COL_TRAIT, self::TYPE, self::COL_PUBLIC];

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

		$typeValidation = Validate::getInArrayRule(self::getValidTraitTypes(), 'required|max:255');
		$this->addCustomRule(self::TYPE,$typeValidation);

		parent::__construct($attributes);
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$npcTrait = new self();
			return $npcTrait->setUploadValues($row);
		};

		$runOnUpdate = function($row){
			$npcTrait = self::where(self::ID, $row[self::ID])->first();
			if($npcTrait==null){
				Logging::error("Could not update, Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			return $npcTrait->setUploadValues($row);
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
		return NonPlayerCharacter::getAllValidTraitTypes();
	}

	public static function download($fileName)
	{
		return DownloadHelper::getDownloadFile(self::all(),$fileName);
	}

}
