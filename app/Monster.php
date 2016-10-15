<?php

namespace App;

use App\Services\Logging;
use App\Services\Validate;

class Monster extends GenericModel
{
	protected $guarded = [self::OWNER_ID, self::APPROVED];

	private $logging;

	const NAME = 'name', HIT_POINTS = 'hit_points', SKILLS = 'skills', LANGUAGES = 'languages', CHALLENGE = 'challenge', ABILITIES = 'abilities', ACTIONS = 'actions', FOUND = 'found', DESCRIPTION = 'description', SPEED = 'speed', ARMOR = 'armor', XP = 'xp', SENSES = 'senses', STATS = 'stats';

	const UPLOAD_COLUMNS = [self::NAME, self::HIT_POINTS, self::SKILLS, self::LANGUAGES, self::CHALLENGE, self::ABILITIES, self::ACTIONS, self::FOUND, self::DESCRIPTION, self::SPEED, self::ARMOR, self::XP, self::SENSES, self::STATS, self::COL_PUBLIC];

	function __construct(array $attributes = array())
	{
		$this->logging = new Logging(self::class);
		parent::__construct($attributes);
	}

	protected $rules = [
		self::NAME => 'required|max:255',
		self::HIT_POINTS => 'required',
		self::ARMOR => 'required|min:0|integer',
		self::XP => 'required|min:0|integer',
		self::SPEED=>'required|min:0|integer',
		self::CHALLENGE =>'required|min:0.0|numeric',
		self::STATS => 'required|json',
	];

	public function user()
	{
		return $this->belongsTo('App\User', self::OWNER_ID);
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

		$this->setJsonFromRowIfPresent(self::ABILITIES, $row);
		$this->setJsonFromRowIfPresent(self::ACTIONS, $row);
		$this->setJsonFromRowIfPresent(self::FOUND, $row);
		$this->setJsonFromRowIfPresent(self::SENSES, $row);
		$this->setJsonFromRowIfPresent(self::SKILLS, $row);
		$this->setJsonFromRowIfPresent(self::LANGUAGES, $row);
		$this->setJsonFromRowIfPresent(self::STATS, $row);

		return $this->runUpdateOrSave();
	}

	public function validate($overrideDefaultValidationRules = false)
	{
		$validAbilitiesArray = [
			$this->statsValid(),
			$this->hitPointsValid(),
			$this->validAbilities(),
			$this->validActions(),
			$this->validFound(),
			$this->validSenses(),
			$this->validSkills(),
			$this->validLanguages()
		];

		return parent::validate($overrideDefaultValidationRules) && Validate::allInArrayTrue($validAbilitiesArray);
	}

	private function statsValid(){
		return $this->setErrorOnFailed(self::STATS, function(){
			return Stats::validStatsArray($this->stats);
		});
	}

	private function hitPointsValid(){
		return $this->setErrorOnFailed(self::HIT_POINTS, function(){
			return Validate::validRoll($this->{self::HIT_POINTS});
		});
	}

	private function validAbilities(){
		return $this->setErrorOnFailed(self::ABILITIES, function(){
			$requiredKeys = ['name', 'description'];
			return Validate::stringOfJsonArrayContainsKeys($this->{self::ABILITIES}, $requiredKeys, true);
		});
	}

	private function validActions(){
		return $this->setErrorOnFailed(self::ACTIONS, function() {
			$requiredKeys = ['name', 'description'];
			return Validate::stringOfJsonArrayContainsKeys($this->{self::ACTIONS}, $requiredKeys, true);
		});
	}

	private function validFound(){
		return $this->setErrorOnFailed(self::FOUND, function() {
			$requiredKeys = ['found'];
			return Validate::stringOfJsonArrayContainsKeys($this->{self::FOUND}, $requiredKeys, true);
		});
	}

	private function validSenses(){
		return $this->setErrorOnFailed(self::SENSES, function() {
			$requiredKeys = ['sense'];
			return Validate::stringOfJsonArrayContainsKeys($this->{self::SENSES}, $requiredKeys, true);
		});
	}

	private function validSkills(){
		return $this->setErrorOnFailed(self::SKILLS, function() {
			$requiredKeys = ['skill', 'modifier'];
			$allRequiredKeysPresent =  Validate::stringOfJsonArrayContainsKeys($this->{self::SKILLS}, $requiredKeys, true);
			if($allRequiredKeysPresent && !Validate::blackOrNull($this->{self::SKILLS})){
				foreach (json_decode($this->{self::SKILLS}) as $skill){
					$modifier = $skill->modifier;
					if(!is_integer($modifier) || is_integer($modifier) && $modifier < 0){
						return false;
					}
				}
				return true;
			}else{
				return $allRequiredKeysPresent;
			}
		});
	}

	private function validLanguages(){
		return $this->setErrorOnFailed(self::LANGUAGES, function() {
			$requiredKeys = ['language'];
			return Validate::stringOfJsonArrayContainsKeys($this->{self::LANGUAGES}, $requiredKeys, true);
		});
	}
}
