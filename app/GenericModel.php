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
use App\Services\AddBatchAssets;
use App\Services\DownloadHelper;
use App\Services\StringUtils;

abstract class GenericModel extends Model implements Upload, Download
{
	protected $rules = array();

	private $logging;

	protected $errors;

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

	public static function findById($id){
		return self::where("id", "=", $id)->get()->first();
	}

	public function safeUpdate(Request $request = null, $overrideDefaultValidationRules = false){
		if($request != null){
			$this->fill($request->all());
		}
		$valid = $this->validate($overrideDefaultValidationRules);
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
		$this->setIfFieldNotPresent('public', function () {
			return $this->getDefaultPublicValue();
		});
	}

	private function getDefaultPublicValue()
	{
		return false;
	}

	private function setOwnerId()
	{
		$this->setIfFieldNotPresent('owner_id', function () {
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
		$this->setIfFieldNotPresent('approved', function () {
			return $this->getDefaultApprovedValue();
		});
	}

	private function getDefaultApprovedValue()
	{
		return false;
	}

	public function setIfFieldNotPresent($field, $funct)
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
			$this->errors = $v->errors();

			return false;
		}
		return true;
	}

	public function errors()
	{
		return $this->errors;
	}

	public function getErrors()
	{
		return ($this->errors() != null) ? $this->errors()->toArray() : new \stdClass();
	}

	public function getErrorsJson(){
		return json_encode($this->getErrors());
	}

	public function getErrorMessage($action = null){
		$action = ($action == null) ? (isset($this->id)) ? 'update' : 'save' : $action;
		if(count($this->errors()) > 0){
			return "Could not $action: ".$this->errors();
		}else{
			return "No errors present";
		}
	}

	public function addCustomRule($field, $rule){
		$this->rules[$field] = $rule;
	}

	public function duplicateFound(){
		$attributeArray = [];
		foreach ($this->getAttributes() as $key => $value){
			if($this->validFieldForLookingForDuplicate($key)){
				array_push($attributeArray, [$key, "=",$value]);
			}
		}
		$value = self::where($attributeArray)->get();

		if(count($value) > 1 || ((count($value) == 1 ) && (!isset($this[self::ID]) || $value[0][self::ID] != $this[self::ID]))){
			$this->setError("duplication_error", "Duplicate object found.")
;			return true;
		}

		return false;
	}

	public function getRules(){
		return $this->rules;
	}

	protected function getUniqueWithIgnoreSelfRule($table, $column=null, $additionalRules = false){
		if($this->{self::ID} != null){
			$validateRule = Validate::getUniqueWithIgnoreSelfRule($table, $this->{self::ID}, $column, $additionalRules);
		}else{
			$validateRule = "unique:$table|$additionalRules";
		}
		return $validateRule;
	}

	private function validFieldForLookingForDuplicate($field){
		return !in_array($field, $this->ignoreWhenLookingForDuplicateArray);
	}

	protected function addIgnoreWhenLookingForDuplicate($field){
		array_push($this->ignoreWhenLookingForDuplicateArray, $field);
	}

	public function setError($field, $error)
	{
		if($this->errors){
			$this->errors->add($field, $error);
		}else{
			$this->errors = new MessageBag();
			$this->errors->add($field, $error);
		}
	}

	public function setErrors($errors, $overrideCurrentErrors = false){
		$this->errors = ($overrideCurrentErrors) ? new MessageBag() : $this->errors;

		foreach ($errors as $field => $errorArray){
			foreach ($errorArray as $error){
				$this->setError($field, $error);
			}
		}
	}

	public static function runUpload($filePath, $uploadColumns){
		$addBatch = new AddBatchAssets($filePath, $uploadColumns);

		$runOnCreate = function($row){
			$model = self::getNewSelf();
			return $model->setUploadValues($row);
		};

		$runOnUpdate = function($row){
			return self::attemptUpdate($row);
		};

		return $addBatch->addBatch($runOnCreate, $runOnUpdate);
	}

	public static function attemptUpdate($row){
		if(!array_has($row, self::ID)){
			Logging::error("Could not update, row provided has no id", self::class);
			return false;
		}

		$dbRow = self::where(self::ID, $row[self::ID])->first();
		if($dbRow == null){
			Logging::error("Could not update, Id ".$row[self::ID]." not found", self::class);
			return false;
		}
		return $dbRow->setUploadValues($row);
	}

	public static function download($fileName)
	{
		return DownloadHelper::getDownloadFile(self::all(),$fileName);
	}


	public function setErrorOnFailed($field, $func,$errorMessage = null){
		$errorMessage = ($errorMessage == null) ? StringUtils::display($field)." invalid." : $errorMessage;
		$valid = $func();
		if(!$valid){
			$this->setError($field, $errorMessage);
		}
		return $valid;
	}


}