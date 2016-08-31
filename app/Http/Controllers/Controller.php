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

	private $controllerNameSpace;

	protected function setControllerNameSpace($controllerNameSpace){
		$this->controllerNameSpace = $controllerNameSpace;
	}

	private function getPostLocation(){
		return url($this->controllerNameSpace);
	}

	protected function getCreateHeaders(){
		return (object) ["createOrUpdate" => "Create", "postLocation" => $this->getPostLocation(), "methodField" => "POST", "addOrSave" => "Add"];
	}

	protected function getUpdateHeaders($id){
		return (object) ["createOrUpdate" => "Update", "postLocation" => $this->getPostLocation()."/".$id, "methodField" => "PATCH", "addOrSave" => "Save"];
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
}
