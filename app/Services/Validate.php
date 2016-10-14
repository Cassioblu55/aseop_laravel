<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/26/16
 * Time: 12:11 AM
 */

namespace app\Services;
use App\GenericModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class Validate
{
	public static function validUpdateData(Request $request, $rules){
		return count(self::getValidationErrors($request, $rules)) == 0;
	}

	public static function getValidationErrors(Request $request, $rules){
		return self::getArrayValidationErrors($request->all(), $rules);
	}

	public static function validArrayData($array, $rules){
		return (count(self::getArrayValidationErrors($array, $rules)) == 0);
	}

	public static function getArrayValidationErrors($array, $rules){
		$v = Validator::make($array, $rules);
		if ($v->fails()) {
			return $v->errors();
		}
		return [];
	}


	public static function validUpdateDataFromGenericModel(Request $request, GenericModel $genericModel){
		$errors = self::getValidationErrors($request, $genericModel->getRules());
		if(count($errors) > 0){
			$genericModel -> setErrors($errors);
			return false;
		}
		return true;
	}


	public static function getErrorMessage(Request $request, $rules, $action = null){
		$action = ($action == null) ? (isset($request->id)) ? 'update' : 'save' : $action;
		$v = Validator::make($request->all(), $rules);
		if ($v->fails()) {
			return "Could not $action: ".$v->errors();
		}
		return "No errors present";
	}

	public static function getInArrayRule($array, $additionalRules=false){
		$arrayRule = "in:".implode(",", $array);
		return (!$additionalRules) ? $arrayRule : $arrayRule."|".$additionalRules;
	}

	public static function getUniqueWithIgnoreSelfRule($table, $id,$column=null, $additionalRules = false){
		$column = ($column == null) ? "id" : $column;
		$uniqueRule = "unique:$table,$column,$id";
		return (!$additionalRules) ? $uniqueRule : $uniqueRule."|".$additionalRules;
	}

	public static function validRollString($rollString){
		$rolls = explode(",", $rollString);
		if(count($rolls) == 0){return false;}

		foreach ($rolls as $roll){
			if(!self::validRoll($roll)){
				return false;
			}
		}
		return true;
	}

	public static function validRoll($roll){
		$rollPattern = "/([0-9]+)([dD])([0-9]+)[+-]([0-9]+)/";
		return preg_match($rollPattern, $roll) == 1;
	}

	public static function blackOrNull($string){
		return $string == '' || $string == null;
	}

	public static function allInArrayTrue($array){
		foreach ($array as $row){
			if($row != true || $row != 1){
				return false;
			}
		}
		return true;
	}

	public static function stringOfJsonArrayContainsKeys($jsonArrayAsString, $requiredKeys, $canBeNullOrBlank = false){

		$blackOrNull =  Validate::blackOrNull($jsonArrayAsString);
		if($canBeNullOrBlank && $jsonArrayAsString == "[]"){return true;}
		if($canBeNullOrBlank && $blackOrNull){return true;}
		if(!$canBeNullOrBlank && $blackOrNull){return false;}

		$json = json_decode($jsonArrayAsString);
		if($json == null || !is_array($json)){return false;}

		foreach ($json as $row){
			if(!array_has($row, $requiredKeys)){
				return false;
			}
		}
		return true;
	}

}