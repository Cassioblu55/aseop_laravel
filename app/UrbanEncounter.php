<?php

namespace App;

use App\Services\Logging;
use App\Services\AddBatchAssets;
use App\Services\DownloadHelper;

class UrbanEncounter extends Random
{
	protected $guarded = [self::OWNER_ID, self::APPROVED];

	private $logging;

	const TITLE = "title", DESCRIPTION = "description", ROLLS = "rolls";

	const UPLOAD_COLUMNS = [self::TITLE, self::DESCRIPTION, self::ROLLS, self::COL_PUBLIC];

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
			$urbanEncounter = new self();
			return $urbanEncounter->setUploadValues($row);
		};

		$runOnUpdate = function($row){
			return self::attemptUpdate($row);
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	public static function getNewSelf(){
		return new self();
	}

	public function setUploadValues($row)
	{
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();

		return $this->runUpdateOrSave();
	}

}