<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;
use App\Services\DownloadHelper;

class Trap extends GenericModel 
{

	protected $guarded = [];
	
	private $logging;

	const NAME = 'name', DESCRIPTION = 'description', ROLLS = 'rolls', WEIGHT = 'weight';

	const UPLOAD_COLUMNS = [self::NAME, self::DESCRIPTION, self::ROLLS, self::WEIGHT];
	
	protected $rules = [
		self::NAME => 'required|max:255',
		self::DESCRIPTION => 'required',
	];
	
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
			$trap = new self();
			return $trap->setUploadValues($row);
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

}