<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;
use App\Services\DownloadHelper;
use App\Services\Validate;

class TavernTrait extends AssetTrait implements Upload
{
	protected $guarded = [self::OWNER_ID, self::APPROVED];

	private $logging;

	const TYPE = 'type';
	const COL_TRAIT = 'trait';

	const UPLOAD_COLUMNS = [self::COL_TRAIT, self::TYPE, self::COL_PUBLIC];

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

		$typeValidation = Validate::getInArrayRule(self::getValidTraitTypes(), 'required|max:255');
		$this->addCustomRule(self::TYPE,$typeValidation);

		parent::__construct($attributes);
	}

	public function validate($overrideDefaultValidationRules = false)
	{
		return parent::validate($overrideDefaultValidationRules) && !$this->duplicateFound();
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

	public static function getValidTraitTypes()
	{
		return Tavern::getAllValidTraitTypes();
	}
}
