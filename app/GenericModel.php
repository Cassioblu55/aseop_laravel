<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/15/16
 * Time: 1:53 AM
 */

namespace App;
use App\Services\Logging;
use App\Services\Validate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Database\QueryException;

abstract class GenericModel extends Model implements Upload, Download
{
	protected $rules = array();

	private $logging;

	protected $errors;

	public function setError($field, $error)
	{
		if($this->errors){
			$this->errors->add($field, $error);
		}else{
			$this->errors = new MessageBag();
			$this->errors->add($field, $error);
		}
	}

	public function setErrors($errors){
		$this->errors = $errors;
	}

	const ID = 'id';
	const COL_PUBLIC = 'public';
	const APPROVED = 'approved';
	const OWNER_ID = 'owner_id';

	private $defaultRules = [
		self::COL_PUBLIC => 'required|boolean',
		self::APPROVED => 'required|boolean',
		self::OWNER_ID => 'required|integer|exists:users,id'
	];

	private $ignoreWhenLookingForDuplicateArray = [self::ID, self::APPROVED, self::COL_PUBLIC, self::CREATED_AT, self::UPDATED_AT];

	const DEFAULT_ADDITIONAL_REQUIRED_COLUMNS = [self::ID, self::COL_PUBLIC, self::APPROVED];

	function __construct(array $attributes = array(), $callingClassName = self::class)
	{
		$this->logging = new Logging($callingClassName);
		parent::__construct($attributes);
	}

	public function safeSave($overrideDefaultValidationRules = false){
		if($this->validate($overrideDefaultValidationRules)){
			try{
				$this->save();
				return true;
			}catch(QueryException $e){
				$this->setError("database error", $e);
				$this->logging->logError($this->getErrorMessage());
				return false;
			}
		}else{
			$this->logging->logError($this->getErrorMessage());
			return false;
		}
	}

	public function safeUpdate(Request $request = null, $overrideDefaultValidationRules = false){
		if($request != null){
			$rules = $this->getAllRules($overrideDefaultValidationRules);
			$errors = Validate::getValidationErrors($request, $rules);

			$valid = count($errors) > 0;
		}else{
			$valid = $this->validate($overrideDefaultValidationRules);
		}
		if($valid) {
			try {
				($request == null) ? $this->update() : $this->update($request->all());
				return true;
			} catch (QueryException $e) {
				$this->setError("database error", $e);
				$this->logging->logError($this->getErrorMessage());
				return false;
			}
		}else{
			if(isset($errors)){$this->setErrors($errors);}
			$this->logging->logError($this->getErrorMessage());
			return false;
		}
	}

	private function getAllRules($overrideDefaultValidationRules = false){
		return ($overrideDefaultValidationRules) ? $this->rules : array_merge($this->rules, $this->defaultRules);
	}

	public function setRequiredMissing()
	{
		$this->setApproved();
		$this->setPublic();
		$this->setOwnerId();
	}

	private function setPublic()
	{
		$this->setIfFeildNotPresent('public', function () {
			return $this->getDefaultPublicValue();
		});
	}

	private function getDefaultPublicValue()
	{
		return false;
	}

	private function setOwnerId()
	{
		$this->setIfFeildNotPresent('owner_id', function () {
			return $this->getDefaultOwnerIdValue();
		});
	}

	public function runUpdateOrSave($overrideDefaultValidationRules = false){
		return isSet($this->id) ? $this->safeUpdate(null,$overrideDefaultValidationRules) : $this->safeSave($overrideDefaultValidationRules);
	}


	private function getDefaultOwnerIdValue()
	{
		return Auth::user()->id;
	}

	private function setApproved()
	{
		$this->setIfFeildNotPresent('approved', function () {
			return $this->getDefaultApprovedValue();
		});
	}

	private function getDefaultApprovedValue()
	{
		return false;
	}

	public function setIfFeildNotPresent($field, $funct)
	{
		if (!isSet($this[$field])) {
			$this[$field] = $funct();
		}
	}

	public function addUploadColumns($row, $columns)
	{
		foreach ($columns as $column) {
			if (isSet($row[$column])) {
				$this[$column] = $row[$column];
			}
		}
	}

	public function allRequiredPresent($arrayOfRequiredFields)
	{
		foreach ($arrayOfRequiredFields as $field) {
			if (!isset($this[$field]) || $this[$field] == '') {
				return false;
			}
		}
		return true;
	}

	public function presentValuesEqual($row)
	{
		$dbRecord = self::where(self::ID, $row[self::ID])->first();

		foreach ($row as $key => $value) {
			if ($dbRecord[$key] != $value) {
				return false;
			}
		}
		return true;
	}

	public function setJsonFromRowIfPresent($field, $row, $default=null)
	{
		if (isset($row[$field])) {
			$this[$field] = stripcslashes($row[$field]);
		}else if(isset($default)){
			$this[$field] = $default;
		}
	}

	public function validate($overrideDefaultValidationRules = false)
	{
		$rules = $this->getAllRules($overrideDefaultValidationRules);
		return $this->runValidation($rules);
	}

	protected function runValidation($rules, array $attributes = null){
		$attributes = ($attributes == null) ? $this->attributesToArray() : $attributes;

		$v = Validator::make($attributes, $rules);
		if ($v->fails()) {
			// set errors and return false
			$this->errors = $v->errors();

			return false;
		}
		// validation pass
		return true;
	}

	public function errors()
	{
		return $this->errors;
	}

	public function getErrorMessage($action = null){
		$action = ($action == null) ? (isset($this->id)) ? 'update' : 'save' : $action;
		if(count($this->errors()) > 0){
			return "Could not $action: ".$this->errors();
		}else{
			return "No errors present";
		}
	}

	protected function addCustomRule($field, $rule){
		$this->rules[$field] = $rule;
	}

	protected function getInArrayRule($array, $additionalRules=false){
		$arrayRule = "in:".implode(",", $array);
		return (!$additionalRules) ? $arrayRule : $arrayRule."|".$additionalRules;
	}

	protected function duplicateFound(){
		$attributeArray = [];
		foreach ($this->getAttributes() as $key => $value){
			if($this->validFieldForLookingForDuplicate($key)){
				array_push($attributeArray, [$key, "=",$value]);
			}
		}
		$value = self::where($attributeArray)->get();

		if(count($value) > 1 || ((count($value) == 1 ) && (!isset($this[self::ID]) || $value[0][self::ID] != $this[self::ID]))){
			$this->errors = json_encode(['duplicationError' => 'Duplicate record found']);
			return true;
		}

		return false;
	}

	public function getRules(){
		return $this->rules;
	}

	protected function getUniqueWithIgnoreSelfRule($table, $column=null, $additionalRules = false){
		$column = ($column == null) ? $table : $column;
		$id  = (isset($this->id)) ? ",".$this->id : '';
		$uniqueRule = "unique:$table,$column".$id;
		return (!$additionalRules) ? $uniqueRule : $uniqueRule."|".$additionalRules;
	}

	private function validFieldForLookingForDuplicate($field){
		return !in_array($field, $this->ignoreWhenLookingForDuplicateArray);
	}

	protected function addIgnoreWhenLookingForDuplicate($field){
		array_push($this->ignoreWhenLookingForDuplicateArray, $field);
	}

}