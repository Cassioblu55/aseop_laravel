<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ProjectRoute;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	private $controllerNameSpace;

	protected function setControllerNameSpace($controllerNameSpace){
		$this->controllerNameSpace = $controllerNameSpace;
	}

	private function getPostLocation(){
		return ProjectRoute::makeRoute($this->controllerNameSpace);
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
}
