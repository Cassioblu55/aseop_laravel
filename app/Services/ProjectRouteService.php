<?php


Class ProjectRoute{

	public static function getProjectBase(){
		return env('PROJECT_BASE')."/";
	}

	public static function makeRoute($url){
		$projectBase = env('PROJECT_BASE');
		return "$projectBase/$url";
	}

}
