<?php

namespace App;

use App\Services\Logging;
use App\Services\Utils;
use App\Services\AddBatchAssets;
use App\Services\DownloadHelper;
use App\Services\Validate;

class Dungeon extends Asset
{
	protected $guarded = [];

	private $logging;

	const TRAIT_TABLE = DungeonTrait::class;

	const NAME = 'name', PURPOSE = 'purpose', HISTORY = 'history', LOCATION = 'location', CREATOR = 'creator', MAP='map',TRAPS = 'traps', SIZE = 'size', OTHER_INFORMATION = 'other_information';

	const FILLABLE_FROM_TRAIT_TABLE = [self::NAME, self::PURPOSE, self::HISTORY, self::LOCATION, self::CREATOR];

	const UPLOAD_COLUMNS = [self::NAME, self::PURPOSE, self::HISTORY, self::LOCATION, self::CREATOR, self::SIZE, self::OTHER_INFORMATION, self::MAP, self::TRAPS, self::COL_PUBLIC];

	const SMALL = "S";
	const MEDIUM = "M";
	const LARGE = "L";
	const VALID_SIZE_OPTIONS = [self::SMALL, self::MEDIUM, self::LARGE];

	protected $rules = [
		self::NAME =>'required|max:255',
		self::MAP => 'required|json',
		self::TRAPS => 'required|json'
	];

	public function user()
	{
		return $this->belongsTo('App\User', self::OWNER_ID);
	}

	function __construct(array $attributes= array())
	{
		$this->logging = new Logging(self::class);
		$class = self::TRAIT_TABLE;

		$sizeValidation = Validate::getInArrayRule(self::VALID_SIZE_OPTIONS, 'required|size:1');
		$this->addCustomRule(self::SIZE,$sizeValidation);

		parent::__construct($attributes,new $class() ,self::FILLABLE_FROM_TRAIT_TABLE);
	}

	public static function generate(){
		return self::generateIncomplete();
	}

	private static function generateIncomplete(){
		$dungeon = new Dungeon();
		$dungeon->setRandomMissing();
		return  $dungeon;
	}

	private function setRandomMissing(){
		$this->setIfFieldNotPresent(self::SIZE, function(){
			return Utils::getRandomFromArray(self::VALID_SIZE_OPTIONS);
		});
		$this->setRequiredMissing();
		$this->setTraps();
		$this->setFillable();
	}

	private function setTraps(){
		$this->setIfFieldNotPresent(self::TRAPS, function(){
			return '[]';
		});
	}

	public static function getNewSelf(){
		return new self();
	}

	public static function upload($filePath)
	{
		return self::runUpload($filePath, self::UPLOAD_COLUMNS);
	}

	public function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		$this->setJsonFromRowIfPresent(self::MAP, $row);
		$this->setJsonFromRowIfPresent(self::TRAPS, $row);
		$this->setTraps();

		return $this->runUpdateOrSave();
	}


}