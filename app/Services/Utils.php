<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/5/16
 * Time: 2:31 PM
 */

namespace App\Services;


class Utils
{

	const LETTERS = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

	public static function getRandomFromArray($array){
		return $array[rand(0, count($array)-1)];
	}

	public static function getRandomKeyFromHash($hash){
		return self::getRandomFromArray(array_keys($hash));
	}

	public static function getBellCurveRange($configData) {
		$configData = (object) $configData;
		$min = $configData->min;
		$max = $configData->max;
		$std_deviation = $configData->std;
		$step = (isset($configData->step)) ? $configData->step : 1;
		return self::purebell($min, $max, $std_deviation, $step);
	}

	private static function purebell($min, $max, $std_deviation, $step = 1) {
		$rand1 = ( float ) mt_rand () / ( float ) mt_getrandmax ();
		$rand2 = ( float ) mt_rand () / ( float ) mt_getrandmax ();
		$gaussian_number = sqrt ( - 2 * log ( $rand1 ) ) * cos ( 2 * M_PI * $rand2 );
		$mean = ($max + $min) / 2;
		$random_number = ($gaussian_number * $std_deviation) + $mean;
		$random_number = round ( $random_number / $step ) * $step;
		if ($random_number < $min || $random_number > $max) {
			$random_number = self::purebell( $min, $max, $std_deviation );
		}
		return $random_number;
	}

	public static function getLetterByNumber($number){
		if($number < count(self::LETTERS)){
			return self::LETTERS[$number];
		}
		return null;
	}

}