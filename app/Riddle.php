<?php

namespace App;
use App\Services\AddBatchAssets;
use App\Services\Logging;
use App\Services\DownloadHelper;

class Riddle extends Random implements Upload
{
	protected $guarded = [self::OWNER_ID, self::APPROVED];

	private $logging;

	const NAME = 'name', RIDDLE = 'riddle',SOLUTION = 'solution', HINT = 'hint', WEIGHT = 'weight', OTHER_INFORMATION = 'other_information';

	const UPLOAD_COLUMNS = [self::NAME, self::RIDDLE, self::SOLUTION, self::HINT, self::WEIGHT, self::OTHER_INFORMATION, self::COL_PUBLIC];

	protected $rules =[
		self::NAME =>'required|max:255',
		self::RIDDLE => 'required',
		self::SOLUTION => 'required'
	];


	const REQUIRED_COLUMNS = [self::NAME,self::RIDDLE,self::SOLUTION];

	public function user()
	{
		return $this->belongsTo('App\User', self::OWNER_ID);
	}

	function __construct(array $attributes = array())
	{
		$this->logging = new Logging(self::class);
		parent::__construct($attributes);
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$riddle = new self();
			return $riddle->setUploadValues($row);
		};

		$runOnUpdate = function($row){
			return self::attemptUpdate($row);
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	public static function getNewSelf(){
		return new self();
	}

	public function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();

		return $this->runUpdateOrSave();
	}

	public function isValid(){
		$allRequiredPresent = $this->allRequiredPresent(self::REQUIRED_COLUMNS);
		return $allRequiredPresent;
	}

}
