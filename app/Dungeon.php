<?php

namespace App;

use App\Services\Logging;
use App\Services\Utils;
use App\Services\AddBatchAssets;

class Dungeon extends Asset implements Upload
{
	protected $guarded = [];

	private $logging;

	const TRAIT_TABLE = DungeonTrait::class;

	const NAME = 'name', PURPOSE = 'purpose', HISTORY = 'history', LOCATION = 'location', CREATOR = 'creator', MAP='map',TRAPS = 'traps', SIZE = 'size', OTHER_INFORMATION = 'other_information';

	const FILLABLE_FROM_TRAIT_TABLE = [self::NAME, self::PURPOSE, self::HISTORY, self::LOCATION, self::CREATOR];

	const UPLOAD_COLUMNS = [self::NAME, self::PURPOSE, self::HISTORY, self::LOCATION, self::CREATOR, self::SIZE, self::OTHER_INFORMATION, self::MAP, self::TRAPS];

	const SMALL = "S";
	const MEDIUM = "M";
	const LARGE = "L";
	const VALID_SIZE_OPTIONS = [self::SMALL, self::MEDIUM, self::LARGE];

	protected $rules = [
		self::NAME =>'required|max:255',
		self::SIZE => 'required|in:'.self::SMALL.','.self::MEDIUM.','.self::LARGE,
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
		$this->setIfFeildNotPresent(self::SIZE, function(){
			return Utils::getRandomFromArray(self::VALID_SIZE_OPTIONS);
		});
		$this->setRequiredMissing();
		$this->setTraps();
		$this->setFillable();
	}

	private function setTraps(){
		$this->setIfFeildNotPresent(self::TRAPS, function(){
			return '[]';
		});
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$dungeon = new self();
			$dungeon->setUploadValues($row);
			return (isSet($dungeon->id));
		};

		$runOnUpdate = function($row){
			$dungeon = self::where(self::ID, $row[self::ID])->first();
			if($dungeon==null){
				Logging::error("Could not update, Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			$dungeon->setUploadValues($row);
			return ($dungeon->presentValuesEqual($row));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		$this->setJsonFromRowIfPresent(self::MAP, $row);
		$this->setJsonFromRowIfPresent(self::TRAPS, $row);
		$this->setTraps();

		if($this->validate()){
			isSet($this->id) ? $this->update() : $this->save();
		}else{
			$this->logging->logError($this->getErrorMessage());
		}
	}

}