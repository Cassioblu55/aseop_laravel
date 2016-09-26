<?php

namespace App;

use App\Services\Logging;
use Illuminate\Database\Eloquent\Model;
use App\Services\AddBatchAssets;

class Spell extends GenericModel implements Upload
{
    protected $guarded = [];

	private $logging;

	const NAME = 'name', COL_CLASS = 'class', LEVEL = 'level', CASTING_TIME = 'casting_time',RANGE = 'range', COMPONENTS = 'components', DURATION = 'duration', DESCRIPTION = 'description', TYPE = 'type';

	const UPLOAD_COLUMNS = [self::NAME, self::COL_CLASS, self::LEVEL, self::CASTING_TIME, self::RANGE, self::COMPONENTS, self::DURATION, self::DESCRIPTION, self::TYPE];

	const FIGHTER = 'fighter', MONK = 'monk', CLERIC = 'cleric', DRUID = 'druid', ROGUE = 'rogue', RANGER = 'ranger', SORCERER = 'sorcerer', WIZARD = 'wizard', WARLOCK = 'warlock', BARBARIAN = 'barbarian', PALADIN = 'paladin', BARD = 'bard';

	const VALID_CLASS_TYPES = [self::FIGHTER, self::MONK, self::CLERIC, self::DRUID, self::ROGUE, self::RANGER, self::SORCERER, self::WIZARD, self::WARLOCK, self::BARBARIAN, self::PALADIN, self::BARD];

	const ABJURATION = 'abjuration', ALCHEMY = 'alchemy', APPORTATION = 'apportation',CHARM_MAGIC = 'charm_magic',
		CONJURATION_MAGIC = 'conjuration_magic',DIVINATION = 'divination',ELEMENTAL_MAGIC = 'elemental_magic', ENCHANTMENT = 'enchantment', EVOCATION = 'evocation', HEALING_MAGIC ='healing_magic',ILLUSION = 'illusion',
		INVOCATION = 'invocation', NATURE_MAGIC = 'nature_magic', NECROMANCY = 'necromancy', SCRYING = 'scrying', THAUMATURGY = 'thaumaturgy', TRANSFORMATION = 'transformation';

	const VALID_SPELL_TYPES = [self::ABJURATION, self::ALCHEMY, self::APPORTATION, self::CHARM_MAGIC, self::CONJURATION_MAGIC, self::DIVINATION, self::ELEMENTAL_MAGIC, self::ENCHANTMENT, self::EVOCATION, self::HEALING_MAGIC, self::ILLUSION, self::INVOCATION, self::NATURE_MAGIC, self::NECROMANCY, self::SCRYING, self::THAUMATURGY, self::TRANSFORMATION];

	protected $rules = [
		self::LEVEL => 'required|min:0|max:9|integer',
		self::RANGE => 'required|min:0|integer',
		self::DESCRIPTION => 'required'
	];

	function __construct(array $attributes = array())
	{
		$this->logging = new Logging(self::class);

		$classValidation = $this->getInArrayRule(self::VALID_CLASS_TYPES, 'required|max:255');
		$this->addCustomRule(self::COL_CLASS,$classValidation);

		$nameValidation = $this->getUniqueWithIgnoreSelfRule("spells", self::NAME, 'required|max:255');
		$this->addCustomRule(self::NAME,$nameValidation);

		$typeValidation = $this->getInArrayRule(self::VALID_SPELL_TYPES, 'required|max:255');
		$this->addCustomRule(self::TYPE,$typeValidation);

		parent::__construct($attributes);
	}

	public function user()
    {
        return $this->belongsTo('App\User', self::OWNER_ID);
    }

    public static function upload($filePath)
    {
	    $addBatch = new AddBatchAssets($filePath, self::UPLOAD_COLUMNS);

	    $runOnCreate = function($row){
		    $spell = new self();
		    $spell->setUploadValues($row);
		    return (isSet($spell->id));
	    };

	    $runOnUpdate = function($row){
		    $spell = self::where(self::ID, $row[self::ID])->first();
		    if($spell==null){
			    Logging::error("Could not update, Id ".$row[self::ID]." not found", self::class);
			    return false;
		    }
		    $spell->setUploadValues($row);
		    return ($spell->presentValuesEqual($row));
	    };
	    return $addBatch->addBatch($runOnCreate, $runOnUpdate);
    }

	private function setUploadValues($row){
		$this->addUploadColumns($row, self::UPLOAD_COLUMNS);
		$this->setRequiredMissing();
		if($this->validate()){
			isSet($this->id) ? $this->update() : $this->save();
		}else{
			$this->logging->logError($this->getErrorMessage());
		}
	}

}