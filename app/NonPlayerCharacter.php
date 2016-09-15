<?php

namespace App;

use App\Services\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NonPlayerCharacter extends Asset
{
	protected $guarded = [];

	const TRAIT_TABLE = NonPlayerCharacterTrait::class;
	const FILLABLE_FROM_TRAIT_TABLE = ['last_name', 'flaw','interaction', 'mannerism','bond','appearance','talent','ideal','ability'];

	const FEMALE = 'F';
	const MALE = 'M';
	const NONE = 'N';
	const VALID_SEX_OPTIONS = [self::MALE => 'Male', self::FEMALE => 'Female', self::NONE => 'None'];

	const AGE_RANGE = ['min'=>16, 'max'=>50, 'std'=>5];

	const MALE_HEIGHT_RANGE = ['min'=>54, 'max'=>78, 'std'=>2.9];
	const FEMALE_HEIGHT_RANGE = ['min'=>48, 'max'=>72, 'std'=>2.7];

	const MALE_WEIGHT_RANGE = ['min'=>110, 'max'=>250, 'std'=>29];
	const FEMALE_WEIGHT_RANGE = ['min'=>90, 'max'=>190, 'std'=>20];

	function __construct(array $attributes= array()){
		$class = self::TRAIT_TABLE;
		parent::__construct($attributes,new $class() ,self::FILLABLE_FROM_TRAIT_TABLE);
	}

	public static function generate(){
		$npc = new NonPlayerCharacter();
		$npc->setMissing();
		$npc['owner_id'] = Auth::user()->id;
		$npc['approved'] = false;
		$npc->save();
		return $npc;
	}

	public static function all($columns = ['*'], $blackList=true){
		$columns = is_array($columns) ? $columns : func_get_args();
		$instance = new static;

		if($blackList == true){
			return $instance->newQuery()->whereNotIn('id', self::getBlackListedIds())->get($columns);
		}else{
			return $instance->newQuery()->get($columns);
		}

	}

	private static function getBlackListedIds(){
		$villains = DB::table('villains')->pluck('npc_id')->toArray();
		return array_merge($villains);
	}

	public function user(){
		return $this->belongsTo('App\User', 'owner_id');
	}

	public function rules(){
		return $this->hasMany('App\Settlement', 'ruler_id');
	}

	public function owns(){
		return $this->hasMany('App\Tavern', 'tavern_owner_id');
	}

	public function villainous(){
		return $this->hasOne('App\Villain', 'npc_id');
	}

	public function displayHeight(){
		return floor($this->height/12)."' ".$this->height%12 .'"';
	}

	public function displaySex(){
		return (array_key_exists($this->sex, self::VALID_SEX_OPTIONS)) ? self::VALID_SEX_OPTIONS[$this->sex] : 'Other';
	}

	public function displayName(){
		return $this->first_name." ".$this->last_name;
	}

	public function setMissing(){
		$this->setPublic();
		$this->setFillable();
		$this->setSex();
		$this->setName();
		$this->setAge();
		$this->setHeight();
		$this->setWeight();
	}

	private function setSex(){
		$this->setIfFeildNotPresent('sex', function(){
			return Utils::getRandomKeyFromHash(self::VALID_SEX_OPTIONS);
		});
	}

	private function setName() {
		$this->setIfFeildNotPresent('first_name', function(){
			if($this->sex == self::MALE){
				return $this->getTraitRandomByType('male_name');
			}else{
				return $this->getTraitRandomByType('female_name');
			}
		});
	}

	private function setAge() {
		$this->setIfFeildNotPresent('age', function(){
			return Utils::getBellCurveRange(self::AGE_RANGE);
		});
	}

	private function setHeight() {
		$this->setIfFeildNotPresent('height', function(){
			if($this->sex == self::MALE){
				return Utils::getBellCurveRange(self::MALE_HEIGHT_RANGE);
			}else{
				return Utils::getBellCurveRange(self::FEMALE_HEIGHT_RANGE);
			}
		});
	}

	private function setWeight() {
		$this->setIfFeildNotPresent('weight', function(){
			if($this->sex == self::MALE){
				return Utils::getBellCurveRange(self::MALE_WEIGHT_RANGE);
			}else{
				return Utils::getBellCurveRange(self::FEMALE_WEIGHT_RANGE);
			}
		});
	}
}
