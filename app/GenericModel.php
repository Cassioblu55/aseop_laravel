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

	const ID = 'id';
	const COL_PUBLIC = 'public';
	const APPROVED = 'approved';

	const DEFAULT_ADDITIONAL_REQUIRED_COLUMNS = [self::ID,self::COL_PUBLIC, self::APPROVED];

	function __construct(array $attributes = array()){
		parent::__construct($attributes);
	}

	protected function setRequiredMissing(){
		$this->setApproved();
		$this->setPublic();
		$this->setOwnerId();
	}

	private function setPublic(){
		$this->setIfFeildNotPresent('public', function(){
			return $this->getDefaultPublicValue();
		});
	}

	private function getDefaultPublicValue(){
		return false;
	}

	private function setOwnerId(){
		$this->setIfFeildNotPresent('owner_id', function(){
			return $this->getDefaultOwnerIdValue();
		});
	}

	private function getDefaultOwnerIdValue(){
		return Auth::user()->id;
	}

	private function setApproved(){
		$this->setIfFeildNotPresent('approved', function(){
			return $this->getDefaultApprovedValue();
		});
	}

	private function getDefaultApprovedValue(){
		return false;
	}

	public function setIfFeildNotPresent($field, $funct){
		if(!isSet($this[$field])){
			$this[$field] = $funct();
		}
	}

	public function addUploadColumns($row, $columns){
		foreach ($columns as $column){
			if(isSet($row[$column])){
				$this[$column] = $row[$column];
			}
		}
	}

	public function allRequiredPresent($arrayOfRequiredFields){
		foreach ($arrayOfRequiredFields as $field){
			if(!isset($this[$field]) || $this[$field] ==''){return false;}
		}
		return true;
	}

	public function presentValuesEqual($row){
		foreach ($row as $key => $value){
			if($this[$key] != $value){return false;}
		}
		return true;
	}

	public function setJsonFromRowIfPresent($field, $row){
		if(isset($row[$field])){
			$this[$field] = stripcslashes($row[$field]);
		}
	}

}