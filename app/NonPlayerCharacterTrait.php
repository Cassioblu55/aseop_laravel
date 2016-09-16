<?php

namespace App;

use App\Services\AddBatchAssets;

class NonPlayerCharacterTrait extends AssetTrait implements Upload
{
	protected $guarded = [];

	const TYPE = 'type';
	const COL_TRAIT = 'trait';

	const UPLOAD_COLUMNS = [self::COL_TRAIT, self::TYPE];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$npcTrait = new self();
			$npcTrait->setUploadValues($row);
			return (isSet($npcTrait->id));
		};

		$runOnUpdate = function($row){
			$npcTrait = self::where(self::ID, $row[self::ID])->first();
			$npcTrait->setUploadValues($row);
			return (isSet($npcTrait->id));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		isSet($this->id) ? $this->update() : $this->save();
	}

}
