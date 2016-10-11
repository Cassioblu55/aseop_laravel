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

	public function logError($message, $ignoreDefaltPrefix = false, $alwaysLog=false){
		if(self::shouldLog() || $alwaysLog){
			if($ignoreDefaltPrefix){
				Log::error($message);
			}else{
				Log::error($this->logWithPrefix($message));
			}
		}
	}

	public function logJson($object, $ignoreDefaltPrefix = false, $alwaysLog=false){
		if(self::shouldLog() || $alwaysLog) {
			try{
				$message = json_encode($object);
			}catch (\Exception $e){
				$message = "Object could not be converted to json.";
			}

			if ($ignoreDefaltPrefix) {
				Log::info($message);
			} else {
				Log::info($this->logWithPrefix($message));
			}
		}
	}

	public function logWarning($message, $ignoreDefaltPrefix = false, $alwaysLog=false){
		if(self::shouldLog() || $alwaysLog) {
			if ($ignoreDefaltPrefix) {
				Log::warning($message);
			} else {
				Log::warning($this->logWithPrefix($message));
			}
		}
	}

	public function logInfo($message, $ignoreDefaltPrefix = false, $alwaysLog=false){
		if(self::shouldLog() || $alwaysLog) {
			if ($ignoreDefaltPrefix) {
				Log::info($message);
			} else {
				Log::info($this->logWithPrefix($message));
			}
		}
	}

	public static function log($message, $class=null, $alwaysLog=false){
		if(self::shouldLog() || $alwaysLog) {
			if($class != null){
				return Log::info($class . ": " . $message);
			}else{
				return Log::info($message);
			}
		}
	}

	public static function error($message, $class=null, $alwaysLog=false){
		if(self::shouldLog() || $alwaysLog) {
			if($class != null){
				return Log::error($class . ": " . $message);
			}else{
				return Log::error($message);
			}
		}
	}

	public function ping(){
		$this->logInfo('ping');
	}

	private static function shouldLog(){
		return env('LOG', false);
	}

	private function logWithPrefix($message){
		return $this->classCallingName.": ".$message;
	}

}