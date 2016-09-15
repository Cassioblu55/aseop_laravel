<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/15/16
 * Time: 1:53 AM
 */

namespace app;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


abstract class GenericModel extends Model
{

	function __construct(array $attributes = array()){
		parent::__construct($attributes);
	}

	protected function setPublic(){
		$this->setIfFeildNotPresent('public', function(){
			return false;
		});
	}

	protected function setIfFeildNotPresent($field, $funct){
		if(!isSet($this[$field])){
			$this[$field] = $funct();
		}
	}

	protected function setOwnerId(){
		$this['owner_id'] = Auth::user()->id;
	}

	protected function setApproved(){
		$this['approved'] = false;
	}

}