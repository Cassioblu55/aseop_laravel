<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;
use App\Services\DownloadHelper;
use App\Services\Validate;

class ForestEncounter extends Random implements Upload
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

	public function validate($overrideDefaultValidationRules = false)
	{
		return parent::validate($overrideDefaultValidationRules) && $this->rollsValid();
	}

	private function rollsValid(){
		if($this->rolls == ""){
			return true;
		}else{
			$validRollString = Validate::validRollString($this->rolls);
			if(!$validRollString){
				$this->setError(self::ROLLS, "Roll string invalid");
			}
			return $validRollString;
		}
	}
}