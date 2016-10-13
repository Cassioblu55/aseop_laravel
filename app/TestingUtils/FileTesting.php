<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/7/16
 * Time: 12:31 PM
 */

namespace App\TestingUtils;
use TestCase;

class FileTesting extends TestCase
{

	private $filePath;
	private $startingText;
	private $newFile;
	private $isFile;

	public function __construct($filePath, $newFile=false)
	{
		$filePathGiven = str_contains($filePath, ".");
		if($newFile && $filePathGiven){
			self::assertFileNotExists($filePath);
		}else if(!$newFile && $filePathGiven){
			self::assertFileExists($filePath);
			$this->startingText = file_get_contents($filePath);
		}else if(!$newFile && !$filePathGiven){
			self::assertTrue(is_dir($filePath));
		}else if($newFile && !$filePathGiven){
			self::assertFileNotExists($filePath);
		}

		$this->isFile = $filePathGiven;
		$this->newFile = $newFile;
		$this->filePath = $filePath;

		parent::__construct();
	}

	public function revert(){
		if($this->newFile && $this->isFile) {
			$this->deleteFile();
		}else if($this->newFile && $this->exists() && !$this->isFile){
			rmdir($this->filePath);
		}else if(!$this->newFile && $this->isFile()){
			file_put_contents($this->filePath, $this->startingText);
		}
	}

	public function clear(){
		file_put_contents($this->filePath, "");
	}

	public function getFileContents(){
		if($this->exists() && $this->isFile()){
			return file_get_contents($this->filePath);
		}else{
			return null;
		}
	}

	public function assertFileContentsEqual($contents){
		self::assertFileExists($this->filePath);
		$this->assertEquals($contents, $this->getFileContents());
	}

	public function exists(){
		return file_exists($this->filePath);
	}

	public function getFilePath(){
		return $this->filePath;
	}

	public function getStartingText(){
		return $this->startingText;
	}

	public function isFile(){
		return $this->isFile;
	}

	public function deleteFile(){
		if($this->exists()){
			unlink($this->filePath);
		}
	}

}