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
		$v = Validator::make($request->all(), $rules);
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


}