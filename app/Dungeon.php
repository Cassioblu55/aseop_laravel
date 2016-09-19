<?php

namespace App;

use App\Services\Utils;
use Illuminate\Support\Facades\Auth;
use App\Services\AddBatchAssets;

class Dungeon extends Asset implements Upload
{
	protected $guarded = [];

	const TRAIT_TABLE = DungeonTrait::class;

	const NAME = 'name', PURPOSE = 'purpose', HISTORY = 'history', LOCATION = 'location', CREATOR = 'creator', MAP='map',TRAPS = 'traps', SIZE = 'size', OTHER_INFORMATION = 'other_information';

	const FILLABLE_FROM_TRAIT_TABLE = [self::NAME, self::PURPOSE, self::HISTORY, self::LOCATION, self::CREATOR];

	const UPLOAD_COLUMNS = [self::NAME, self::PURPOSE, self::HISTORY, self::LOCATION, self::CREATOR, self::SIZE, self::OTHER_INFORMATION, self::MAP, self::TRAPS];

	const REQUIRED_COLUMNS = [self::NAME, self::MAP, self::SIZE, self::TRAPS];

	const SMALL = "S";
	const MEDIUM = "M";
	const LARGE = "L";
	const VALID_SIZE_OPTIONS = [self::SMALL, self::MEDIUM, self::LARGE];

	public function user()
	{
		return $this->belongsTo('App\User', self::OWNER_ID);
	}

	function __construct(array $attributes= array())
	{
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
			$dungeonTrait = new self();
			$dungeonTrait->setUploadValues($row);
			return (isSet($dungeonTrait->id));
		};

		$runOnUpdate = function($row){
			$dungeonTrait = self::where(self::ID, $row[self::ID])->first();
			$dungeonTrait->setUploadValues($row);
			return ($dungeonTrait->presentValuesEqual($row));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		$this->setJsonFromRowIfPresent(self::MAP, $row);
		$this->setJsonFromRowIfPresent(self::TRAPS, $row);
		$this->setTraps();

		if($this->isValid()){
			isSet($this->id) ? $this->update() : $this->save();
		}
	}

	public function isValid(){
		$allRequiredPresent = $this->allRequiredPresent(self::REQUIRED_COLUMNS);
		$vaildSize = in_array($this->size, self::VALID_SIZE_OPTIONS);
		return $allRequiredPresent && $vaildSize;
	}

}