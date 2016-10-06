<?php

namespace App;

use App\Services\AddBatchAssets;
use App\Services\Logging;
use App\Services\DownloadHelper;
use App\Services\Validate;

class VillainTrait extends AssetTrait implements Upload
{
    protected $guarded = [];

	private $logging;

	const TYPE = 'type';
	const KIND = 'kind';
	const DESCRIPTION = 'description';

	const UPLOAD_COLUMNS = [self::KIND, self::TYPE, self::DESCRIPTION, self::COL_PUBLIC];

	const WEAKNESS = 'weakness';
	const METHOD = 'method';
	const SCHEME = 'scheme';

	const VALID_TYPES = [self::WEAKNESS, self::METHOD, self::SCHEME];

	const VALID_SCHEME_KINDS = ['Immortality','Influence','Magic','Mayhem','Passion','Power','Revenge','Wealth'];

	const VALID_WEAKNESS_KINDS = ['Hidden Object','Love Avenged','Artifact','Special Weapon','Truth Revealed','Ancient Prophecy','Forgiveness','Mystic Bargain'];

	const VALID_METHOD_KINDS = ['Agricultural devastation','Bounty hunting or assassination','Captivity or coercion','Confidence scams','Defamation','Dueling','Execution','Impersonation or disguise','Lying or perjury','Magical mayhem','Murder','Neglect','Politics','Religion','Stalking','Theft or Property Crime','Torture','Vice','Warfare'];

    public function user()
    {
        return $this->belongsTo('App\User', self::OWNER_ID);
    }

	protected $rules = [];

	function __construct(array $attributes = array())
	{
		$this->logging = new Logging(self::class);

		$typeValidation = Validate::getInArrayRule(self::VALID_TYPES, 'required|max:255');
		$this->addCustomRule(self::TYPE,$typeValidation);

		parent::__construct($attributes);
	}

	public static function upload($filePath)
	{
		$addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

		$runOnCreate = function($row){
			$villainTrait = new self();
			return $villainTrait->setUploadValues($row);
		};

		$runOnUpdate = function($row){
			$villainTrait = self::where(self::ID, $row[self::ID])->first();
			if($villainTrait==null){
				Logging::error("Could not update, Id ".$row[self::ID]." not found", self::class);
				return false;
			}
			return $villainTrait->setUploadValues($row);
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();

		return $this->runUpdateOrSave();
	}

	public function validate($overrideDefaultValidationRules = false)
	{
		$firstRoundValid = parent::validate($overrideDefaultValidationRules);

		$secondRoundValidationRules = [
				self::KIND => $this->getKindValidationRule()
			];

		return ($firstRoundValid ? $this->runValidation($secondRoundValidationRules) : $firstRoundValid ) && !$this->duplicateFound();
	}

	private function getKindValidationRule(){
		$additionalValidationRules = "required|max:255";
		switch ($this->type) {
			case self::WEAKNESS:
				$rule = Validate::getInArrayRule(self::VALID_WEAKNESS_KINDS, $additionalValidationRules);
				break;
			case self::SCHEME:
				$rule = Validate::getInArrayRule(self::VALID_SCHEME_KINDS, $additionalValidationRules);
				break;
			case self::METHOD:
				$rule = Validate::getInArrayRule(self::VALID_METHOD_KINDS, $additionalValidationRules);
				break;
			default:
				$rule = $additionalValidationRules;
		}
		return $rule;
	}


	public static function getValidKindsByType(){
		return [
			self::WEAKNESS => self::VALID_WEAKNESS_KINDS,
			self::SCHEME => self::VALID_SCHEME_KINDS,
			self::METHOD => self::VALID_METHOD_KINDS
		];
	}

	public static function getValidTraitTypes(){
		return self::VALID_TYPES;
	}

	public static function download($fileName)
	{
		return DownloadHelper::getDownloadFile(self::all(),$fileName);
	}

}