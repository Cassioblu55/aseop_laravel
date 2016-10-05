<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;
use App\Services\DownloadHelper;
use App\Services\Validate;

class TavernTrait extends AssetTrait implements Upload
{
	protected $guarded = [];

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


	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$tavernTrait = new self();
			return $tavernTrait->setUploadValues($row);
		};

		$runOnUpdate = function($row){
			$tavernTrait = self::where(self::ID, $row[self::ID])->first();
			if($tavernTrait==null){
				Logging::error("Could not update, Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			return $tavernTrait->setUploadValues($row);
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();

		return $this->runUpdateOrSave();
	}

	public static function getValidTraitTypes()
	{
		return Tavern::getAllValidTraitTypes();
	}

	public static function download($fileName, $ext = 'csv')
	{
		DownloadHelper::getDownloadFile(self::all(),$fileName, $ext);
	}
}
