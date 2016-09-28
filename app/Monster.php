<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;

class Monster extends GenericModel
{
	protected $guarded = [];

	private $logging;
	
	const NAME = 'name', HIT_POINTS = 'hit_points', SKILLS = 'skills', LANGUAGES = 'languages', CHALLENGE = 'challenge', ABILITIES = 'abilities', ACTIONS = 'actions', FOUND = 'found', DESCRIPTION = 'description', SPEED = 'speed', ARMOR = 'armor', XP = 'xp', SENSES = 'senses', STATS = 'stats';

	const UPLOAD_COLUMNS = [self::NAME, self::HIT_POINTS, self::SKILLS, self::LANGUAGES, self::CHALLENGE, self::ABILITIES, self::ACTIONS, self::FOUND, self::DESCRIPTION, self::SPEED, self::ARMOR, self::XP, self::SENSES, self::STATS];

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
		self::ABILITIES=>'json',
		self::ACTIONS =>'json',
		self::SENSES => 'json',
		self::FOUND => 'json',
		self::LANGUAGES => 'json',
		self::SKILLS => 'json'
	];

	public function user()
	{
		return $this->belongsTo('App\User', self::OWNER_ID);
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$monster = new self();
			return $monster->setUploadValues($row);
		};

		$runOnUpdate = function($row){
			$monster = self::where(self::ID, $row[self::ID])->first();
			if($monster==null){
				Logging::error("Could not update, Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			return $monster->setUploadValues($row);
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();

		$this->setJsonFromRowIfPresent(self::ABILITIES, $row, "[]");
		$this->setJsonFromRowIfPresent(self::ACTIONS, $row, "[]");
		$this->setJsonFromRowIfPresent(self::FOUND, $row, "[]");
		$this->setJsonFromRowIfPresent(self::SENSES, $row, "[]");
		$this->setJsonFromRowIfPresent(self::SKILLS, $row, "[]");
		$this->setJsonFromRowIfPresent(self::LANGUAGES, $row, "[]");
		$this->setJsonFromRowIfPresent(self::STATS, $row, "[]");

		return $this->runUpdateOrSave();
	}
}
