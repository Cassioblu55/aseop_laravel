<?php

namespace App;

use App\Services\AddBatchAssets;

class ForestEncounter extends Random implements Upload
{
    protected $guarded = [];
	
	const TITLE = "title", DESCRIPTION = "description", ROLLS = "rolls";
	const REQUIRED_COLUMNS = [self::TITLE, self::DESCRIPTION];

	const UPLOAD_COLUMNS = [self::TITLE, self::DESCRIPTION, self::ROLLS];

    public function user()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$forestEncounter = new self();
			$forestEncounter->setUploadValues($row);
			return (isSet($forestEncounter->id));
		};

		$runOnUpdate = function($row){
			$forestEncounter = self::where(self::ID, $row[self::ID])->first();
			$forestEncounter->setUploadValues($row);
			return ($forestEncounter->presentValuesEqual($row));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		$this->setJsonFromRowIfPresent(self::ROLLS, $row);

		if($this->isValid()){
			isSet($this->id) ? $this->update() : $this->save();
		}
	}

	public function isValid(){
		return $this->allRequiredPresent(self::REQUIRED_COLUMNS) ;
	}
	
}