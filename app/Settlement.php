<?php

namespace App;
use App\Services\Logging;
use App\Services\Utils;
use app\Services\Validate;
use App\Services\AddBatchAssets;
use Illuminate\Http\Request;

class Settlement extends Asset implements Upload
{
	private $logging;

	const SMALL = "S";
	const MEDIUM = "M";
	const LARGE = "L";
	const VALID_SIZE_OPTIONS = [self::SMALL =>'Small', self::MEDIUM=>'Medium', self::LARGE=>'Large'];

	const SIZE = 'size', NAME = 'name', KNOWN_FOR = 'known_for', NOTABLE_TRAITS = 'notable_traits', RULER_STATUS = 'ruler_status', CURRENT_CALAMITY = 'current_calamity', RULER_ID = 'ruler_id', POPULATION = 'population', OTHER_INFORMATION = 'other_information', RACE_RELATIONS = 'race_relations';

	protected $guarded = [self::OWNER_ID, self::APPROVED];

	const UPLOAD_COLUMNS = [self::NAME, self::KNOWN_FOR, self::NOTABLE_TRAITS, self::RULER_ID, self::RULER_STATUS, self::CURRENT_CALAMITY, self::POPULATION, self::SIZE, self::OTHER_INFORMATION, self::RACE_RELATIONS, self::COL_PUBLIC];

	const TRAIT_TABLE = SettlementTrait::class;

	const FILLABLE_FROM_TRAIT_TABLE = [self::NAME, self::KNOWN_FOR, self::NOTABLE_TRAITS, self::RULER_STATUS, self::CURRENT_CALAMITY, self::RACE_RELATIONS];

	const SMALL_POPULATION_RANGE = ['min'=>20, 'max'=>75, 'std'=>5];
	const MEDIUM_POPULATION_RANGE = ['min'=>76, 'max'=>300, 'std'=>10];
	const LARGE_POPULATION_RANGE = ['min'=>300, 'max'=>1500, 'std'=>100];

	protected $rules = [
		self::SIZE => 'required|in:'.self::SMALL.','.self::MEDIUM.','.self::LARGE,
		self::NAME =>'required|max:255',
		self::RULER_ID => 'required|integer|exists:non_player_characters,id',
		self::POPULATION => 'required|integer|min:0'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

	public function ruler()
	{
		return $this->belongsTo('App\NonPlayerCharacter', 'ruler_id');
	}

	function __construct(array $attributes= array())
	{
		$this->logging = new Logging(self::class);
		$class = self::TRAIT_TABLE;
		parent::__construct($attributes,new $class() ,self::FILLABLE_FROM_TRAIT_TABLE);
	}

	public function getSizeDisplay(){
		return self::VALID_SIZE_OPTIONS[$this->size];
	}

	public static function generate(){
		$settlement = new Settlement();
		$settlement->setMissing();
		$settlement->runUpdateOrSave();
		return $settlement;
	}

	public function setMissing(){
		$this->setSize();
		$this->setPopulation();
		$this->setFillable();
		$this->setRequiredMissing();
		$this->setRuler();
	}

	private function setSize(){
		$this->setIfFieldNotPresent(self::SIZE, function(){
			return Utils::getRandomKeyFromHash(self::VALID_SIZE_OPTIONS);
		});
	}

	private function setRuler(){
		$this->setIfFieldNotPresent(self::RULER_ID, function(){
			return $this->createNewRulerOnlyIfSettlementValidOtherwise();
		});
	}

	private function createNewRulerOnlyIfSettlementValidOtherwise(){
		$valid = Validate::validWithIgnoredRule($this->attributesToArray(), $this->rules, self::RULER_ID);
		return ($valid) ? NonPlayerCharacter::generate()->id : null;
	}

	private function setPopulation(){
		$this->setIfFieldNotPresent(self::POPULATION, function(){
			switch ($this->size){
				case self::SMALL:
					$configData = self::SMALL_POPULATION_RANGE; break;
				case self::MEDIUM:
					$configData = self::MEDIUM_POPULATION_RANGE; break;
				default:
					$configData = self::LARGE_POPULATION_RANGE; break;
			}
			return Utils::getBellCurveRange($configData);
		});
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

}
