<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/6/16
 * Time: 12:08 PM
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

abstract class GenericCommand extends Command
{

	protected $nameToReplace;
	protected $placeHolder;
	protected $replaceLowercase;
	protected $verbose;


	public function __construct($placeHolder = "Base_name", $verbose=true, $replaceLowercase=true)
	{
		$this->placeHolder = $placeHolder;
		$this->replaceLowercase = $replaceLowercase;
		$this->verbose = $verbose;

		parent::__construct();
	}

	public function createFileFromTemplate($filePath, $baseIdentifier){
		CommandUtils::createFileFromTemplate($filePath, $baseIdentifier, $this->nameToReplace, $this->placeHolder, $this->replaceLowercase);
	}

	public function replaceNames($filePath){
		CommandUtils::replaceNames($filePath, $this->nameToReplace);
	}

	public function getFilePathFromPrefixSuffix($prefix, $suffix, $addBlade = true){
		return $prefix."/".lcfirst($this->nameToReplace)."_".$suffix.($addBlade ? ".blade" : '').".php";
	}

	protected function setNameToReplaceFromIdentifier($identifier){
		$this->nameToReplace = $this->argument($identifier);
	}

	protected function setNameToReplace($nameToReplace){
		$this->nameToReplace = $nameToReplace;
	}

}