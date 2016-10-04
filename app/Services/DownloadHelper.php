<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/28/16
 * Time: 1:46 AM
 */


namespace App\Services;
use Excel;

class DownloadHelper
{

	const VALID_EXPORT_EXTENTIONS = ['csv', 'xls', 'xlsx'];

	public static function getDownloadFile($data, $fileName, $ext='csv', $useTimestamps = true){

		if(in_array($ext, self::VALID_EXPORT_EXTENTIONS)){
			$fileName = ($useTimestamps) ? $fileName."_".date("Y-m-d") : $fileName;
			Excel::create($fileName, function($excel) use($data) {
				$excel->sheet('Sheet 1', function($sheet) use($data) {
					$sheet->fromArray($data);
				});
			})->export($ext);
		}
	}

}