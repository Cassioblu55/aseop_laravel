<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	const SUCCESS_MESSAGE = 'successMessage';

	const DEFAULT_RECORD_UPDATED_MESSAGE = "Record Updated Successfully";
	const DEFAULT_RECORD_ADDED_MESSAGE = "Record Added Successfully";

	const SHOW = "show";
	const EDIT = "edit";
	const INDEX = "index";
	const CREATE =  "create";

	private $controllerNameSpace;
	private $controllerProperName;
	private $controllerViewPrefix;
	private $controllerModelName;

	private $tableName;

	protected function setControllerNames($controllerName){
		$this->setControllerNameSpace($controllerName."s");
		$this->setControllerProperName(ucfirst($controllerName)."Controller");
		$this->setControllerViewPrefix($controllerName);
		$this->setControllerModelName($controllerName);
		$this->setTableName($controllerName.'s');

	}

	protected function setControllerNameSpace($controllerNameSpace){
		$this->controllerNameSpace = $controllerNameSpace;
	}

	protected function setControllerProperName($controllerProperName){
		$this->controllerProperName = $controllerProperName;
	}

	protected function setControllerViewPrefix($controllerViewPrefix){
		$this->controllerViewPrefix = $controllerViewPrefix;
	}

	protected function setControllerModelName($controllerModelName){
		$this->controllerModelName = $controllerModelName;
	}

	protected function setTableName($tableName){
		$this->tableName = $tableName;
	}

	private function getPostLocation(){
		return url($this->controllerNameSpace);
	}

	protected function getTableName(){
		return $this->tableName;
	}

	protected function getControllerAction($controllerAction){
		return $this->controllerProperName.'@'.$controllerAction;
	}

	protected function getControllerView($controllerView){
		return $this->controllerNameSpace.".".$this->controllerViewPrefix."_".$controllerView;
	}

	protected function getCreateHeaders(){
		return (object) ["createOrUpdate" => "Create", "postLocation" => $this->getPostLocation(), "methodField" => "POST", "addOrSave" => "Add", "dataDefaults" => $this->getDefaultAdditionalData()];
	}

	protected function getUpdateHeaders($id){


		return (object) ["createOrUpdate" => "Update", "postLocation" => $this->getPostLocation()."/".$id, "methodField" => "PATCH", "addOrSave" => "Save", "dataDefaults" => $this->getDefaultAdditionalData()];
	}

	protected function getIndexHeaders(){
		return (object) ["dataDefaults" => $this->getDefaultAdditionalData()];
	}

	protected function getShowHeaders(){
		return (object) ["dataDefaults" => $this->getDefaultAdditionalData()];
	}

	protected function getUploadHeaders(){
		return (object) ["postLocation" => $this->getPostLocation()."/upload","addOrSave" => "Add", "methodField" => "POST", "dataDefaults" => $this->getDefaultAdditionalData()];
	}

	private function getDefaultAdditionalData(){
		$data = [];
		$data['model'] = $this->controllerModelName;
		$data['addEditController'] = $this->getJsControllerName("AddEdit");
		$data['indexController'] = $this->getJsControllerName("Index");
		$data['showController'] = $this->getJsControllerName("Show");
		return (object) $data;
	}

	private function getJsControllerName($middle){
		return ucwords($this->controllerModelName.$middle."Controller");
	}


	protected function getPostHeaders(){
		return (object) ["postLocation" => $this->getPostLocation(), "methodField" => "POST"];
	}

	protected static function sendRecordUpdatedSuccessfully($message = self::DEFAULT_RECORD_UPDATED_MESSAGE){
		return [self::SUCCESS_MESSAGE => $message];
	}

	protected static function sendRecordAddedSuccessfully($message = self::DEFAULT_RECORD_ADDED_MESSAGE){
		return [self::SUCCESS_MESSAGE => $message];
	}

	protected function getIndexControllerAction(){
		return $this->getControllerAction(self::INDEX);
	}

	protected function getEditControllerAction(){
		return $this->getControllerAction(self::EDIT);
	}

	protected function getShowControllerAction(){
		return $this->getControllerAction(self::SHOW);
	}

	protected static function addMessages($dataHash, array $urlParams){
		$data = [];
		foreach ($dataHash as $key =>$value){
			$data[$key] = $value;
		}
		foreach ($urlParams as $urlParam => $urlParamValue) {
			$data[$urlParam] = $urlParamValue;
		}
		return $data;
	}

	protected static function addUpdateSuccessMessage($dataHash){
		$urlParams = [self::SUCCESS_MESSAGE => self::DEFAULT_RECORD_UPDATED_MESSAGE];
		return self::addMessages($dataHash, $urlParams);
	}

	protected static function addAddedSuccessMessage($dataHash){
		$urlParams = [self::SUCCESS_MESSAGE => self::DEFAULT_RECORD_ADDED_MESSAGE];
		return self::addMessages($dataHash, $urlParams);
	}

	public function index()
	{
		$headers = $this->getIndexHeaders();
		return view($this->getControllerView('index'), compact('headers'));
	}

}
