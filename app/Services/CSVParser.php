<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 9/15/16
 * Time: 12:51 AM
 */

namespace app\Services;

class CSVParser{

	private $filePath;
	private $columns;
	private $max;
	private $separator;
	private $orderedDataColumns;

	public function __construct($filePath, array $columns, $max=1000, $separator=","){
		$this->columns = $columns;
		$this->filePath = $filePath;
		$this->max = $max;
		$this->separator = $separator;
	}

	public function getCSVData(){
		$csvData = [];

		$data = array_map("str_getcsv", file($this->filePath,FILE_SKIP_EMPTY_LINES));
		$this->setOrderedDataColumns(array_shift($data));

		foreach ($data as $i=>$row) {
			$csvData[$i] = array_combine($this->getOrderedDataColumns(), $row);
		}

		return $csvData;
	}

	private function setOrderedDataColumns($dataColumns){
		$orderedDataColumns = [];
		foreach ($this->columns as $column){
			foreach ($dataColumns as $dataColumn){
				if($column == $dataColumn){
					array_push($orderedDataColumns, $dataColumn);
				}
			}
		}
		$this->orderedDataColumns = $orderedDataColumns;
	}

	private function getOrderedDataColumns(){
		return $this->orderedDataColumns;
	}

}