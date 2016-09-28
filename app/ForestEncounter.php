<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;

class ForestEncounter extends Random implements Upload
{
    protected $guarded = [];

	private $logging;

	const TITLE = "title", DESCRIPTION = "description", ROLLS = "rolls";

	const UPLOAD_COLUMNS = [self::TITLE, self::DESCRIPTION, self::ROLLS];

	protected $rules = [
		self::TITLE => 'required|max:255',
		self::DESCRIPTION => 'required'
	];

    public function user()
    {
        return $this->belongsTo('App\User', self::OWNER_ID);
    }

	function __construct(array $attributes= array())
	{
		$this->logging = new Logging(self::class);
		parent::__construct($attributes);
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$forestEncounter = new self();
			return $forestEncounter->setUploadValues($row);
		};

		$runOnUpdate = function($row){
			$forestEncounter = self::where(self::ID, $row[self::ID])->first();
			if($forestEncounter==null){
				Logging::error("Could not update, Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			return $forestEncounter->setUploadValues($row);
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		return $this->runUpdateOrSave();
	}

}