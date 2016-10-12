<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/6/16
 * Time: 11:48 AM
 */

namespace App\Console\Commands;


class CommandUtils
{
	const BASE_FILE_PATH = '/resources/automation/templates';

	public static function getBaseFileContentsFromIdentifier($baseIdentifier, $useBlade=true, $templatePath=null){
		$path = ($templatePath != null) ? $templatePath : self::BASE_FILE_PATH;
		$fileName = $baseIdentifier.(($useBlade) ? ".blade" : "").".php";
		return file_get_contents(base_path($path."/".$fileName));
	}

	public static function replaceNames($filePath, $nameToReplace, $placeholder="Base_name", $replaceLowercase=true){
		$str=file_get_contents($filePath);

		$str=str_replace($placeholder, $nameToReplace,$str);

		if($replaceLowercase){
			$str=str_replace(lcfirst($placeholder), lcfirst($nameToReplace),$str);
		}

		file_put_contents($filePath, $str);
	}

	public static function createFileFromTemplate($filePath, $baseIdentifier, $replaceWith, $placeHolder="Base_name", $replaceLowercase=true, $useBlade=true, $templatePath=null){

		$file = fopen($filePath, 'w');

		if($file){
			self::setFileContentsFromIdentifier($filePath, $baseIdentifier, $useBlade, $templatePath);
			self::replaceNames($filePath, $replaceWith, $placeHolder, $replaceLowercase);
		}
	}

	public static function setFileContentsFromIdentifier($destinationFile, $baseIdentifier, $useBlade=true, $templatePath=null){
		$data = self::getBaseFileContentsFromIdentifier($baseIdentifier, $useBlade, $templatePath);
		file_put_contents($destinationFile, $data);
	}

	public static function addToFileFromIdentifier($filePathToAppend, $baseIdentifier, $useBlade=true, $templatePath=null){
		$sourceContent = file_get_contents($filePathToAppend);
		$contentToAppend = self::getBaseFileContentsFromIdentifier($baseIdentifier, $useBlade, $templatePath);

		file_put_contents($filePathToAppend, $sourceContent.$contentToAppend);
	}

	public static function getFileDirPath($path){
		$pathArray = explode("/", $path);
		$endOfPath = $pathArray[count($pathArray)-1];
		if(str_contains($endOfPath, ".")){
			array_splice($pathArray, count($pathArray)-1, 1);
		}
		return implode("/",$pathArray);
	}

	public static function composer($params){
		shell_exec(self::getComposerCommand($params));
	}

	public static function getComposerCommand($params){
		$composerLink = env('COMPOSER', 'composer');
		return "$composerLink $params";
	}

}