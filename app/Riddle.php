<?php

namespace App;
use App\Services\AddBatchAssets;
use App\Services\Logging;

class Riddle extends Random implements Upload
{
	protected $guarded = [];

	private $logging;

	const NAME = 'name', RIDDLE = 'riddle',SOLUTION = 'solution', HINT = 'hint', WEIGHT = 'weight', OTHER_INFORMATION = 'other_information';

	const UPLOAD_COLUMNS = [self::NAME, self::RIDDLE, self::SOLUTION, self::HINT, self::WEIGHT, self::OTHER_INFORMATION];

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
			$riddle->setUploadValues($row);
			return (isSet($riddle->id));
		};

		$runOnUpdate = function($row){
			$riddle = self::where(self::ID, $row[self::ID])->first();
			if($riddle==null){
				Logging::log("Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			$riddle->setUploadValues($row);
			return ($riddle->presentValuesEqual($row));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		if($this->validate()){
			isSet($this->id) ? $this->update() : $this->save();
		}else{
			$this->logging->logError($this->getErrorMessage());
		}
	}

	public function isValid(){
		$allRequiredPresent = $this->allRequiredPresent(self::REQUIRED_COLUMNS);
		return $allRequiredPresent;
	}



}
