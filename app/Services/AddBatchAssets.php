<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/15/16
 * Time: 7:41 PM
 */

namespace App\Services;


use App\GenericModel;

class AddBatchAssets extends CSVParser
{

	public function __construct($filePath, array $columns, $doNotUseDefaults=false){
		if(!$doNotUseDefaults){
			$columns = array_merge($columns, GenericModel::DEFAULT_ADDITIONAL_REQUIRED_COLUMNS);
		}
		parent::__construct($filePath, $columns);
	}

	public function addBatch($runOnCreate, $runOnUpdate){
		$createdCount = 0;
		$updatedCount = 0;
		$uploadFailedCount = 0;

		$csvData = $this->getCSVData();

		foreach ($csvData as $row){
			if(isset($row[GenericModel::ID]) && $runOnUpdate($row)){
				$updatedCount++;
			}elseif (!isset($row[GenericModel::ID]) && $runOnCreate($row)){
				$createdCount++;
			}else{
				$uploadFailedCount++;
			}
		}
		return "$createdCount records added $updatedCount updated $uploadFailedCount records could not be uploaded";
	}

}