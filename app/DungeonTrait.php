<?php

namespace App;

use App\Services\AddBatchAssets;

class DungeonTrait extends AssetTrait implements Upload 
{

	protected $guarded = [];

	const TYPE = 'type';
	const COL_TRAIT = 'trait';
	const WEIGHT = 'weight';
	const DESCRIPTION = 'description';

	const UPLOAD_COLUMNS = [self::COL_TRAIT, self::TYPE, self::WEIGHT, self::DESCRIPTION];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
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
			return (isSet($dungeonTrait->id));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		echo $this->weight;
		$this->setRequiredMissing();
		isSet($this->id) ? $this->update() : $this->save();
	}

}
