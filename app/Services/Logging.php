<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/22/16
 * Time: 9:48 PM
 */

namespace App\Services;

use Illuminate\Support\Facades\Log;
use \Exception as Exception;

class Logging{

	private $classCallingName;

	const INFO = 'info', ERROR = 'error', WARNING = 'warning';

	const VALID_LOG_TYPES = [self::INFO, self::ERROR, self::WARNING];

	public function __construct($classCallingName){
		$this->classCallingName = $classCallingName;
	}

	public function logError($message, $ignoreDefaultPrefix = false, $alwaysLog=false){
		self::runLog($message, self::ERROR, $ignoreDefaultPrefix, $alwaysLog);
	}

	public function logWarning($message, $ignoreDefaultPrefix = false, $alwaysLog=false){
		self::runLog($message, self::WARNING, $ignoreDefaultPrefix, $alwaysLog);
	}

	public function logInfo($message, $ignoreDefaultPrefix = false, $alwaysLog=false){
		self::runLog($message, self::INFO, $ignoreDefaultPrefix, $alwaysLog);
	}

	public function logJson($jsonStringOrObject, $ignoreDefaultPrefix = false, $alwaysLog=false){
		try{
			$jsonObject = json_decode($jsonStringOrObject);
			$message = ($jsonObject) ?json_encode($jsonObject) : "Object could not be converted to json.";
		}catch (Exception $e){
			$message = json_encode($jsonStringOrObject);
		}
		self::runLog($message, self::INFO, $ignoreDefaultPrefix, $alwaysLog);
	}

	public function ping($ignoreDefaultPrefix = false, $alwaysLog=false){
		$this->logInfo('ping', $ignoreDefaultPrefix, $alwaysLog);
	}

	private function runLog($message, $type, $ignoreDefaultPrefix = false, $alwaysLog=false){
		$message = $this->getMessageWithPrefix($message, $ignoreDefaultPrefix);
		self::performLog($message, $type, $alwaysLog);
	}

	private function getMessageWithPrefix($message, $ignoreDefaultPrefix = false){
		return ($ignoreDefaultPrefix) ? $message : $this->logWithPrefix($message);
	}

	private static function performLog($message, $type, $alwaysLog=false){
		if(self::shouldLog() || $alwaysLog){
			switch ($type){
				case self::WARNING:{
					Log::warning($message);
					break;
				}case self::ERROR:{
					Log::error($message);
					break;
				}default:
					Log::info($message);
					break;
			}
		}
	}

	private static function shouldLog(){
		return env('LOG', false);
	}

	private function logWithPrefix($message){
		return $this->classCallingName.": ".$message;
	}

	public static function log($message, $class=null, $alwaysLog=false){
		self::runStaticLog($message, self::INFO, $class, $alwaysLog);
	}

	public static function error($message, $class=null, $alwaysLog=false){
		self::runStaticLog($message, self::ERROR, $class, $alwaysLog);
	}

	public static function warning($message, $class=null, $alwaysLog=false){
		self::runStaticLog($message, self::WARNING, $class, $alwaysLog);
	}

	private static function runStaticLog($message, $type, $class=null, $alwaysLog=false){
		$message = self::getMessageWithClass($message, $class);
		self::performLog($message, $type, $alwaysLog);
	}

	private static function getMessageWithClass($message, $class=null){
		return ($class != null) ? $class . ": " . $message : $message;
	}

}