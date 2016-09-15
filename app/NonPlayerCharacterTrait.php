<?php

namespace App;

use App\Services\CSVParser;

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
		$uploadCount = 0;
		$uploadFailedCount = 0;

		$csvParser = new CSVParser($filePath, self::UPLOAD_COLUMNS);
		$csvData = $csvParser->getCSVData();

		foreach ($csvData as $row){
			$npcTrait = new NonPlayerCharacterTrait();
			$npcTrait[self::TYPE]  = $row[self::TYPE];
			$npcTrait[self::COL_TRAIT]  = $row[self::COL_TRAIT];
			$npcTrait->setPublic();
			$npcTrait->setOwnerId();
			$npcTrait->setApproved();

			$npcTrait->save();

			if(isset($npcTrait->id)){
				$uploadCount++;
			}else{
				$uploadFailedCount++;
			}
		}

		return "$uploadCount records added, $uploadFailedCount records could not be uploaded";
	}

}
