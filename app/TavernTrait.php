<?php

namespace App;

use App\Services\AddBatchAssets;

class TavernTrait extends AssetTrait implements Upload
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
			$tavenTrait = new self();
			$tavenTrait->setUploadValues($row);
			return (isSet($tavenTrait->id));
		};

		$runOnUpdate = function($row){
			$tavenTrait = self::where(self::ID, $row[self::ID])->first();
			$tavenTrait->setUploadValues($row);
			return (isSet($tavenTrait->id));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		isSet($this->id) ? $this->update() : $this->save();
	}
}
