<?php

namespace App;
use App\Services\Utils;
use Illuminate\Support\Facades\Auth;

class Settlement extends Asset
{
	protected $guarded = [];

	const TRAIT_TABLE = SettlementTrait::class;
	const FILLABLE_FROM_TRAIT_TABLE = ['name', 'known_for', 'notable_traits','ruler_status', 'current_calamity', 'race_relations'];

	const SMALL = "S";
	const MEDIUM = "M";
	const LARGE = "L";
	const VALID_SIZE_OPTIONS = [self::SMALL =>'Small', self::MEDIUM=>'Medium', self::LARGE=>'Large'];

	const SMALL_POPULATION_RANGE = ['min'=>20, 'max'=>75, 'std'=>5];
	const MEDIUM_POPULATION_RANGE = ['min'=>76, 'max'=>300, 'std'=>10];
	const LARGE_POPULATION_RANGE = ['min'=>300, 'max'=>1500, 'std'=>100];

	public function user()
	{
		return $this->belongsTo('App\User', 'owner_id');
	}

	public function ruler()
	{
		return $this->belongsTo('App\NonPlayerCharacter', 'ruler_id');
	}

	function __construct(array $attributes= array())
	{
		$class = self::TRAIT_TABLE;
		parent::__construct($attributes,new $class() ,self::FILLABLE_FROM_TRAIT_TABLE);
	}

	public function getSizeDisplay(){
		return self::VALID_SIZE_OPTIONS[$this->size];
	}

	protected function getDisplay($value, $displayHash){

	}


	public static function generate(){
		$settlement = new Settlement();
		$settlement->setMissing();
		$settlement['owner_id'] = Auth::user()->id;
		$settlement['approved'] = false;
		$settlement->save();
		return $settlement;
	}

	public function setMissing(){
		$this->setRuler();
		$this->setSize();
		$this->setPopulation();

		$this->setFillable();
		$this->setPublic();
	}

	private function setSize(){
		$this->setIfFeildNotPresent('size', function(){
			return Utils::getRandomKeyFromHash(self::VALID_SIZE_OPTIONS);
		});
	}

	private function setRuler(){
		$this->setIfFeildNotPresent('ruler_id', function(){
			return NonPlayerCharacter::generate()->id;
		});
	}

	private function setPopulation(){
		$this->setIfFeildNotPresent('population', function(){
			switch ($this->size){
				case self::SMALL:
					$configData = self::SMALL_POPULATION_RANGE; break;
				case self::MEDIUM:
					$configData = self::MEDIUM_POPULATION_RANGE; break;
				default:
					$configData = self::LARGE_POPULATION_RANGE; break;
			}
			return Utils::getBellCurveRange($configData);
		});
	}
}
