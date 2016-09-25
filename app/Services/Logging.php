<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/22/16
 * Time: 9:48 PM
 */

namespace App\Services;

use Illuminate\Support\Facades\Log;

class Logging{

	private $classCallingName;

	public function __construct($classCallingName){
		$this->classCallingName = $classCallingName;
	}

	public function logError($message, $ignoreDefaltPrefix = false){
		if(self::shouldLog()){
			if($ignoreDefaltPrefix){
				Log::error($message);
			}else{
				Log::error($this->logWithPrefix($message));
			}
		}
	}

	public function logWarning($message, $ignoreDefaltPrefix = false){
		if(self::shouldLog()) {
			if ($ignoreDefaltPrefix) {
				Log::warning($message);
			} else {
				Log::warning($this->logWithPrefix($message));
			}
		}
	}

	public function logInfo($message, $ignoreDefaltPrefix = false){
		if(self::shouldLog()) {
			if ($ignoreDefaltPrefix) {
				Log::info($message);
			} else {
				Log::info($this->logWithPrefix($message));
			}
		}
	}

	public function ping(){
		$this->logInfo('ping');
	}

	private function shouldLog(){
		return env('LOG', false);
	}

	private function logWithPrefix($message){
		return $this->classCallingName.": ".$message;
	}

}