<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;
use Illuminate\Database\Eloquent\Model;

class SettlementTrait extends AssetTrait implements Upload
{
	protected $guarded = [];

	private $logging;

	const TYPE = 'type';
	const COL_TRAIT = 'trait';

	const UPLOAD_COLUMNS = [self::COL_TRAIT, self::TYPE];

	protected $rules = [
		self::COL_TRAIT => 'required'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

	function __construct(array $attributes= array())
	{
		$this->logging = new Logging(self::class);

		$typeValidation = $this->getInArrayRule(self::getValidTraitTypes(), 'required|max:255');
		$this->addCustomRule(self::TYPE,$typeValidation);

		parent::__construct($attributes);
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$settlementTrait = new self();
			$settlementTrait->setUploadValues($row);
			return (isSet($settlementTrait->id));
		};

		$runOnUpdate = function($row){
			$settlementTrait = self::where(self::ID, $row[self::ID])->first();
			if($settlementTrait==null){
				Logging::log("Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			$settlementTrait->setUploadValues($row);
			return ($settlementTrait->presentValuesEqual($row));
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		$this->logging->logInfo(json_encode($this->id));

		if($this->validate() && !$this->duplicateFound()){
			isSet($this->id) ? $this->update() : $this->save();
		}else{
			$this->logging->logError($this->getErrorMessage());
		}
	}

	public static function getValidTraitTypes()
	{
		return Settlement::FILLABLE_FROM_TRAIT_TABLE;
	}

}
