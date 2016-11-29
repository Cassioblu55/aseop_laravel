<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\GenericModel;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	private $logging;

	private $controllerNameSpace;
	private $controllerProperName;
	private $controllerViewPrefix;
	private $controllerModelName;

	private $tableName;

	function  __construct($callingClassName = self::class){
		$this->logging = new Logging($callingClassName);
	}

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
		return (object) ["postLocation" => $this->getPostLocation()."/upload","addOrSave" => "Upload", "methodField" => "POST", "dataDefaults" => $this->getDefaultAdditionalData()];
	}

	private function getDefaultAdditionalData(){
		$data = [];
		$data['model'] = $this->controllerModelName;
		$data['addEditController'] = $this->getJsControllerName("AddEdit");
		$data['indexController'] = $this->getJsControllerName("Index");
		$data['showController'] = $this->getJsControllerName("Show");
		$data['uploadController'] = $this->getJsControllerName("Upload");
		return (object) $data;
	}

	private function getJsControllerName($middle){
		return ucwords($this->controllerModelName.$middle."Controller");
	}

	protected function getPostHeaders(){
		return (object) ["postLocation" => $this->getPostLocation(), "methodField" => "POST"];
	}

	protected static function sendRecordUpdatedSuccessfully($message = Messages::DEFAULT_RECORD_UPDATED_MESSAGE){
		return [Messages::SUCCESS_MESSAGE => $message];
	}

	protected static function sendRecordAddedSuccessfully($message = Messages::DEFAULT_RECORD_ADDED_MESSAGE){
		return [Messages::SUCCESS_MESSAGE => $message];
	}

	protected static function sendSucessMesage($message){
		return [Messages::SUCCESS_MESSAGE => $message];
	}

	protected static function sendSuccessfullyDeletedMesage(){
		return [Messages::SUCCESS_MESSAGE => Messages::DEFAULT_RECORD_DELETED_MESSAGE];
	}

	protected function getIndexControllerAction(){
		return $this->getControllerAction(Messages::INDEX);
	}

	protected function getCreateControllerAction(){
		return $this->getControllerAction(Messages::CREATE);
	}

	protected function getEditControllerAction(){
		return $this->getControllerAction(Messages::EDIT);
	}

	protected function getShowControllerAction(){
		return $this->getControllerAction(Messages::SHOW);
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

	public static function addUpdatedFailedMessage($dataHash){
		$urlParams = [Messages::ERROR_MESSAGE => Messages::DEFAULT_RECORD_COULD_NOT_BE_UPDATED];
		return self::addMessages($dataHash, $urlParams);
	}

	protected static function addUpdateSuccessMessage($dataHash){
		$urlParams = [Messages::SUCCESS_MESSAGE => Messages::DEFAULT_RECORD_UPDATED_MESSAGE];
		return self::addMessages($dataHash, $urlParams);
	}

	protected static function addAddedSuccessMessage($dataHash){
		$urlParams = [Messages::SUCCESS_MESSAGE => Messages::DEFAULT_RECORD_ADDED_MESSAGE];
		return self::addMessages($dataHash, $urlParams);
	}

	protected static function addAddedFailedMessage($dataHash){
		$urlParams = [Messages::ERROR_MESSAGE => Messages::DEFAULT_RECORD_COULD_NOT_BE_ADDED];
		return self::addMessages($dataHash, $urlParams);
	}

	//TODO Controller adds slashes when saving json data with special characters: "'"

	protected function validateStore(GenericModel $genericModel, $redirectToIndex = false, $modelName = null){
		$modelName = ($modelName == null) ? $genericModel->getTable() : $modelName;
		$data = [$modelName => $genericModel];

		if ($genericModel->safeSave()) {
			$message = ($redirectToIndex) ? self::sendRecordAddedSuccessfully() : self::addAddedSuccessMessage($data);
			$action = ($redirectToIndex) ? $this->getIndexControllerAction() : $this->getShowControllerAction();
		}else{
			$action = $this->getCreateControllerAction();
			$message = self::addAddedFailedMessage($data);
		}
		return redirect()->action($action, $message);
	}

	protected function validateUpdate(Request $request, GenericModel $genericModel, $redirectToIndex = false, $modelName = null){
		$modelName = ($modelName == null) ? $genericModel->getTable() : $modelName;
		$data = [$modelName => $genericModel];

		if($genericModel->safeUpdate($request)){
			$action= ($redirectToIndex) ? $this->getIndexControllerAction() : $this->getShowControllerAction();
			$message= ($redirectToIndex) ? self::sendRecordUpdatedSuccessfully() : self::addUpdateSuccessMessage($data);
		}else{
			$action= $this->getEditControllerAction();
			$message= self::addUpdatedFailedMessage($data);
		}
		return redirect()->action($action, $message);

	}

	public function index()
	{
		$headers = $this->getIndexHeaders();
		return view($this->getControllerView('index'), compact('headers'));
	}

}
