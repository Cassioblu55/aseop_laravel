<?php

namespace App;

use App\Services\Logging;
use App\Services\Utils;
use App\Services\Validate;

class Dungeon extends Asset
{
	protected $guarded = [self::OWNER_ID, self::APPROVED];

	private $logging;

	const TRAIT_TABLE = DungeonTrait::class;

	const NAME = 'name', PURPOSE = 'purpose', HISTORY = 'history', LOCATION = 'location', CREATOR = 'creator', MAP='map',TRAPS = 'traps', SIZE = 'size', OTHER_INFORMATION = 'other_information';

	const FILLABLE_FROM_TRAIT_TABLE = [self::NAME, self::PURPOSE, self::HISTORY, self::LOCATION, self::CREATOR];

	const UPLOAD_COLUMNS = [self::NAME, self::PURPOSE, self::HISTORY, self::LOCATION, self::CREATOR, self::SIZE, self::OTHER_INFORMATION, self::MAP, self::TRAPS, self::COL_PUBLIC];

	const SMALL = "S", MEDIUM = "M", LARGE = "L";
	const VALID_SIZE_OPTIONS = [self::SMALL, self::MEDIUM, self::LARGE];

	const WALKWAY = "w", START = "s", IMPASSABLE = "x", TRAP = "t";
	const VALID_MAP_OPITIONS = [self::WALKWAY, self::START, self::IMPASSABLE, self::TRAP];

	const SMALL_MAP_INTEGER =6, MEDIUM_MAP_INTEGER = 8, LARGE_MAP_INTEGR = 12;
	const MAP_INTEGER_AMOUNT_BY_SIZE = [self::SMALL => self::SMALL_MAP_INTEGER, self::MEDIUM => self::MEDIUM_MAP_INTEGER, self::LARGE => self::LARGE_MAP_INTEGR];

	protected $rules = [
		self::NAME =>'required|max:255',
		self::MAP => 'required|json',
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

	public function generate(){
		$this->setRandomMissing();
		return  $this;
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


	public function validate($overrideDefaultValidationRules = false)
	{
		$validAbilitiesArray = [
			$this->validMap(),
			$this->validTraps()
		];

		return parent::validate($overrideDefaultValidationRules) && Validate::allInArrayTrue($validAbilitiesArray);
	}

	private function validMap(){
		$mapArray = json_decode($this->{self::MAP});
		if($mapArray == null){return false;}

		if(!$this->validMapSize($mapArray)){return false;}

		$rowNumber = 0;
		foreach($mapArray as $mapRow){
			if(!$this->validRow($mapRow, $rowNumber)){return false;}
			$rowNumber++;
		}

		if(!$this->mapStartFound($mapArray)){return false;}

		return true;
	}

	private function validMapSize($mapArray){
		$mapSizeInteger = count($mapArray);
		$correctSize = $this->getMapSizeInteger();
		if($mapSizeInteger != $correctSize){
			$this->setError(self::MAP, "Map in incorrect size, map size: $mapSizeInteger, should be: $correctSize.");
			return false;
		}
		return true;
	}

	public function getMapSizeInteger(){
		if(array_key_exists($this->{self::SIZE},self::MAP_INTEGER_AMOUNT_BY_SIZE)){
			return self::MAP_INTEGER_AMOUNT_BY_SIZE[$this->{self::SIZE}];
		}
		return null;
	}

	private function validRow($mapRow, $rowNumber){
		$correctSize = self::MAP_INTEGER_AMOUNT_BY_SIZE[$this->{self::SIZE}];
		if(is_array($mapRow)){
			$rowSize = count($mapRow);
			if($rowSize != $correctSize){
				$this->setError(self::MAP, "Map is not square. Row $rowNumber has size $rowSize instead of $correctSize.");
				return false;
			}
		}else{
			$this->setError(self::MAP, "Map row $rowNumber invalid.");
			return false;
		}
		return $this->mapRowContainsValidCharacters($mapRow, $rowNumber);
	}

	private function mapRowContainsValidCharacters($mapRow, $rowNumber){
		$columnNumber = 0;
		foreach($mapRow as $squareValue){
			if(!in_array($squareValue, self::VALID_MAP_OPITIONS)){
				$columnLetter = Utils::getLetterByNumber($columnNumber);
				$this->setError(self::MAP, "Map contains invalid square. '$squareValue' invalid, row $rowNumber column $columnLetter.");
				return false;
			}
			$columnNumber++;
		}
		return true;
	}

	private function mapStartFound($mapArray){
		foreach ($mapArray as $mapRow){
			foreach ($mapRow as $squareValue){
				if($squareValue == self::START){
					return true;
				}
			}
		}
		$this->setError(self::MAP, "Map contains no start.");
		return false;
	}

	private function validTraps(){
		if($this->validMap()) {

			if (Validate::blackOrNull($this->{self::TRAPS}) || $this->{self::TRAPS} == "[]") {
				return true;
			}
			$trapsArray = json_decode($this->{self::TRAPS});
			if ($trapsArray == null || !is_array($trapsArray)) {
				$this->setError(self::TRAPS, "Traps invalid.");
				return false;
			}

			if(count($trapsArray) < $this->getNumberOfTrapsInMap()){
				$this->setError(self::MAP, "Map has traps marked not saved in traps.");
				return false;
			}

			$trapNumber = 1;
			foreach ($trapsArray as $trap) {
				if (!is_array($trap)) {
					$this->setError(self::TRAPS, "Trap number $trapNumber invalid, not array.");
					return false;
				}
				if (count($trap) < 3) {
					$this->setError(self::TRAPS, "Trap number $trapNumber invalid, too small.");
					return false;
				}
				foreach ($trap as $trapValue) {
					if (!is_numeric($trapValue) || (is_numeric($trapValue) && !is_integer((int)$trapValue))) {
						$this->setError(self::TRAPS, "Trap number $trapNumber invalid, '$trapValue' not an integer.");
						return false;
					}
				}

				$trapInDatabase = Trap::where(self::ID, intval($trap[0]))->first();
				if ($trapInDatabase == null) {
					$this->setError(self::TRAPS, "Trap number $trapNumber not found in database.");
					return false;
				}

				if ($this->getMapSquare($trap[2], $trap[1]) != self::TRAP) {
					$this->setError(self::TRAPS, "Trap number $trapNumber not marked as trap in map.");
					return false;
				}

				$trapNumber++;
			}

			return true;
		}
		return false;
	}

	public function getMapSquare($column, $row){
		if($this->validMap()){
			$mapArray = json_decode($this->{self::MAP});
			$rowCount = 0;
			foreach ($mapArray as $mapRow){
				$columnCount = 0;
				foreach ($mapRow as $squareValue){
					if($columnCount == $row && $rowCount == $column){
						return $squareValue;
					}
					$columnCount++;
				}
				$rowCount++;
			}
		}else{
			return null;
		}
		return null;
	}

	public function getNumberOfTrapsInMap(){
		if($this->validMap()){
			$trapCount = 0;
			$mapArray = json_decode($this->{self::MAP});
			foreach ($mapArray as $mapRow){
				foreach ($mapRow as $squareValue) {
					if ($squareValue == self::TRAP) {
						$trapCount++;
					}
				}
			}
			return $trapCount;
		}
		return null;
	}

}