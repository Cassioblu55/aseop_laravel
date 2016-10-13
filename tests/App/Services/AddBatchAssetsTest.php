<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/5/16
 * Time: 11:13 PM
 */


class AddBatchAssetsTest extends TestCase
{
	public function testAddBatchShouldRunCreateFunctionsWhenIdsGiven(){
		$path = "resources/assets/testing/csv/AddBatchAssets/testingUploadFileCreate_DO_NOT_EDIT.csv";
		self::assertFileExists($path);

		$addBatchAssets = new \App\Services\AddBatchAssets($path, ['name']);

		$runOnCreate = function($row){
			$this->assertArrayNotHasKey("id", $row);
			$this->assertEquals("foo", $row['name']);
			return true;
		};

		$runOnUpdate = function($row){
			return false;
		};

		$message = $addBatchAssets->addBatch($runOnCreate, $runOnUpdate);
		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);
	}

	public function testAddBatchShouldRunUpdateFunctionsWhenIdsGiven(){
		$path = "resources/assets/testing/csv/AddBatchAssets/testingUploadFileUpdate_DO_NOT_EDIT.csv";
		self::assertFileExists($path);

		$addBatchAssets = new \App\Services\AddBatchAssets($path, ['name']);

		$runOnCreate = function($row){
			return false;
		};

		$runOnUpdate = function($row){
			$this->assertEquals(1, $row['id']);
			$this->assertEquals("foo", $row['name']);
			return true;
		};

		$message = $addBatchAssets->addBatch($runOnCreate, $runOnUpdate);
		$this->assertEquals("0 records added 1 updated 0 records could not be uploaded", $message);
	}

	public function testAddBatchShouldReportMessagesIfFalseIsReturned(){
		$path = "resources/assets/testing/csv/AddBatchAssets/testingUploadFileUpdate_DO_NOT_EDIT.csv";
		self::assertFileExists($path);

		$addBatchAssets = new \App\Services\AddBatchAssets($path, ['name']);

		$runOnCreate = function($row){
			return false;
		};

		$runOnUpdate = function($row){
			return false;
		};

		$message = $addBatchAssets->addBatch($runOnCreate, $runOnUpdate);
		$this->assertEquals("0 records added 0 updated 1 records could not be uploaded", $message);
	}

	public function testAddBatchShouldIgnoreDefaultColumnsLikeIdWhenToldTo(){
		$path = "resources/assets/testing/csv/AddBatchAssets/testingUploadFileUpdate_DO_NOT_EDIT.csv";
		self::assertFileExists($path);

		$addBatchAssets = new \App\Services\AddBatchAssets($path, ['name'], true);

		$runOnCreate = function($row){
			return true;
		};

		$runOnUpdate = function($row){
			return false;
		};

		$message = $addBatchAssets->addBatch($runOnCreate, $runOnUpdate);
		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);
	}

}