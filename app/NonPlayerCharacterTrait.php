<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;
use App\Services\DownloadHelper;
use App\Services\Validate;

class NonPlayerCharacterTrait extends AssetTrait implements Upload
{
	protected $guarded = [self::OWNER_ID, self::APPROVED];

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
			return self::attemptUpdate($row);
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	public static function getNewSelf(){
		return new self();
	}

	public function validate($overrideDefaultValidationRules = false)
	{
		return parent::validate($overrideDefaultValidationRules) && !$this->duplicateFound();
	}

	public function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();

		return $this->runUpdateOrSave();

	}

	public static function getValidTraitTypes()
	{
		return NonPlayerCharacter::getAllValidTraitTypes();
	}


}
