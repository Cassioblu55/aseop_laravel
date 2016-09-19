<?php

namespace App;
use App\Services\AddBatchAssets;

class Riddle extends Random implements Upload
{
	protected $guarded = [];

	const NAME = 'name', RIDDLE = 'riddle',SOLUTION = 'solution', HINT = 'hint', WEIGHT = 'weight', OTHER_INFORMATION = 'other_information';

	const UPLOAD_COLUMNS = [self::NAME, self::RIDDLE, self::SOLUTION, self::HINT, self::WEIGHT, self::OTHER_INFORMATION];

	const REQUIRED_COLUMNS = [self::NAME,self::RIDDLE,self::SOLUTION];

	public function user()
	{
		return $this->belongsTo('App\User', self::OWNER_ID);
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
			$riddle->setUploadValues($row);
			return ($riddle->presentValuesEqual($row));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		if($this->isValid()){
			isSet($this->id) ? $this->update() : $this->save();
		}
	}

	public function isValid(){
		$allRequiredPresent = $this->allRequiredPresent(self::REQUIRED_COLUMNS);
		return $allRequiredPresent;
	}



}
