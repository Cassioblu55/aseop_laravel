<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	const DEFAULT_RECORD_UPDATED_MESSAGE = "Record Updated Successfully";
	const DEFAULT_RECORD_ADDED_MESSAGE = "Record Added Successfully";

	const SHOW = "show";
	const EDIT = "edit";
	const INDEX = "index";

	private $controllerNameSpace;
	private $controllerProperName;
	private $controllerViewPrefix;
	private $controllerModelName;

	protected function setControllerNames($controllerName){
		$this->setControllerNameSpace($controllerName."s");
		$this->setControllerProperName(ucfirst($controllerName)."Controller");
		$this->setControllerViewPrefix($controllerName);
		$this->setControllerModelName($controllerName);
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

	private function getPostLocation(){
		return url($this->controllerNameSpace);
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

	private function getDefaultAdditionalData(){
		$data = [];
		$data['model'] = $this->controllerModelName;
		return (object) $data;
	}


	protected function getPostHeaders(){
		return (object) ["postLocation" => $this->getPostLocation(), "methodField" => "POST"];
	}

	protected static function sendRecordUpdatedSuccessfully($message = self::DEFAULT_RECORD_UPDATED_MESSAGE){
		return ["successMessage" => $message];
	}

	protected static function sendRecordAddedSuccessfully($message = self::DEFAULT_RECORD_ADDED_MESSAGE){
		return ["successMessage" => $message];
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

}
