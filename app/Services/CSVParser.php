<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/15/16
 * Time: 12:51 AM
 */

namespace App\Services;

class CSVParser{

	private $filePath;
	private $columns;
	private $max;
	private $separator;

	public function __construct($filePath, array $columns,$max=1000, $separator=","){
		$this->columns = $columns;
		$this->filePath = $filePath;
		$this->max = $max;
		$this->separator = $separator;
	}

	public function getCSVData(){
		$csvData = [];

		$data = array_map("str_getcsv", file($this->filePath,FILE_SKIP_EMPTY_LINES));

		$desiredColumnsWithIndex = $this->getDesiredColumnsWithIndex(array_shift($data));

		foreach ($data as $row) {
			$rowWithDesiredData = $this->getDesiredData($row, $desiredColumnsWithIndex);
			array_push($csvData, $rowWithDesiredData);
		}

		return $csvData;
	}

	private function getDesiredColumnsWithIndex($dataColumns){
		$desiredColumnsWithIndex = [];
		foreach ($dataColumns as $i => $dataColumn){
			foreach ($this->columns as $column){
				if($dataColumn == $column){
					$desiredColumnsWithIndex[$dataColumn] = $i;
				}
			}
		}
		return $desiredColumnsWithIndex;
	}

	private function getDesiredData($row, $desiredColumnsWithIndex){
		$rowWithDesiredData = [];
		foreach ($desiredColumnsWithIndex as $column => $index){
			$rowWithDesiredData[$column] = $row[$index];
		}
		return $rowWithDesiredData;
	}

}