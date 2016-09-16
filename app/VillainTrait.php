<?php

namespace App;

use App\Services\AddBatchAssets;

class VillainTrait extends AssetTrait implements Upload
{
    protected $guarded = [];

	const TYPE = 'type';
	const KIND = 'kind';
	const DESCRIPTION = 'description';

	const UPLOAD_COLUMNS = [self::KIND, self::TYPE, self::DESCRIPTION];

	const WEAKNESS = 'weakness';
	const METHOD = 'method';
	const SCHEME = 'scheme';

	const VALID_TYPES = [self::WEAKNESS => 'Weakness', self::METHOD => 'Method', self::SCHEME => 'Scheme'];

	const VALID_SCHEME_KINDS = ['Immortality','Influence','Magic','Mayhem','Passion','Power','Revenge','Wealth'];

	const VAILD_WEAKNESS_KINDS = ['Hidden Object','Love Avenged','Artifact','Special Weapon','Truth Revealed','Ancient Prophecy','Forgiveness','Mystic Bargain'];

	const VAILD_METHOD_KINDS = ['Agricultural devastation','Bounty hunting or assassination','Captivity or coercion','Confidence scams','Defamation','Dueling','Execution','Impersonation or disguise','Lying or perjury','Magical mayhem','Murder','Neglect','Politics','Religion','Stalking','Theft or Property Crime','Torture','Vice','Warfare'];

    public function user()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$villainTrait = new self();
			$villainTrait->setUploadValues($row);
			return (isSet($villainTrait->id));
		};

		$runOnUpdate = function($row){
			$villainTrait = self::where(self::ID, $row[self::ID])->first();
			$villainTrait->setUploadValues($row);
			return (isSet($villainTrait->id));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		isSet($this->id) ? $this->update() : $this->save();
	}

	public static function getValidKindsByType(){
		return [
			self::WEAKNESS => self::VAILD_WEAKNESS_KINDS,
			self::SCHEME => self::VALID_SCHEME_KINDS,
			self::METHOD => self::VAILD_METHOD_KINDS
		];
	}

	public static function getVaildTypes(){
		return self::VALID_TYPES;
	}
}