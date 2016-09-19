<?php

namespace App;

use App\Services\AddBatchAssets;

class Monster extends GenericModel
{
	protected $guarded = [];
	
	const NAME = 'name', HIT_POINTS = 'hit_points', SKILLS = 'skills', LANGUAGES = 'languages', CHALLENGE = 'challenge', ABILITIES = 'abilities', ACTIONS = 'actions', FOUND = 'found', DESCRIPTION = 'description', SPEED = 'speed', ARMOR = 'armor', XP = 'xp', SENSES = 'senses', STATS = 'stats';

	const UPLOAD_COLUMNS = [self::NAME, self::HIT_POINTS, self::SKILLS, self::LANGUAGES, self::CHALLENGE, self::ABILITIES, self::ACTIONS, self::FOUND, self::DESCRIPTION, self::SPEED, self::ARMOR, self::XP, self::SENSES, self::STATS];

	const REQUIRED_COLUMNS = [self::NAME, self::HIT_POINTS, self::ARMOR, self::XP, self::SPEED, self::CHALLENGE, self::STATS];


	public function user()
	{
		return $this->belongsTo('App\User', self::OWNER_ID);
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$monster = new self();
			$monster->setUploadValues($row);
			return (isSet($monster->id));
		};

		$runOnUpdate = function($row){
			$monster = self::where(self::ID, $row[self::ID])->first();
			$monster->setUploadValues($row);
			return ($monster->presentValuesEqual($row));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();

		$this->setJsonFromRowIfPresent(self::ABILITIES, $row);
		$this->setJsonFromRowIfPresent(self::ACTIONS, $row);
		$this->setJsonFromRowIfPresent(self::FOUND, $row);
		$this->setJsonFromRowIfPresent(self::SENSES, $row);
		$this->setJsonFromRowIfPresent(self::SKILLS, $row);
		$this->setJsonFromRowIfPresent(self::LANGUAGES, $row);
		$this->setJsonFromRowIfPresent(self::STATS, $row);

		if($this->isValid()){
			isSet($this->id) ? $this->update() : $this->save();
		}
	}

	public function isValid(){
		return $this->allRequiredPresent(self::REQUIRED_COLUMNS);
	}
}
