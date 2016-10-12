<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/4/16
 * Time: 4:03 PM
 */

use App\Dungeon;

use App\DungeonTrait;
use App\GenericModel;
use App\TestingUtils\FileTesting;

class GenericModelTest extends TestCase
{
	private $logging;

	public function __construct()
	{
		$this->logging = new \App\Services\Logging(self::class);

		parent::__construct();
	}

	public function setUp(){
		parent::setUp();

		$user = factory(\App\User::class)->create();
		$this->actingAs($user);
	}

	public function testSetErrorsShouldAddMultipleErrors()
	{
		$genericModel = new Dungeon();

		$this->assertNull($genericModel->errors());

		$errors = ["testError" => ["error one", "error two"]];

		$genericModel->setErrors($errors);

		$this->assertEquals('{"testError":["error one","error two"]}', $genericModel->errors()->toJson());
	}

	public function testSetErrorsShouldRemoveExistingErrorsIfOverrideCurrentErrorsIsSetToTrue()
	{
		$genericModel = new Dungeon();

		$genericModel->validate();

		$this->assertNotNull($genericModel->errors());

		$errors = ["testError" => ["error one", "error two"]];

		$genericModel->setErrors($errors, true);

		$this->assertEquals('{"testError":["error one","error two"]}', $genericModel->errors()->toJson());
	}

	public function testSetErrorShouldAddErrorWhenErrorsEqualsNull(){
		$genericModel = new Dungeon();

		$this->assertNull($genericModel->errors());

		$genericModel->setError("testError", "this is a test error");

		$this->assertEquals('{"testError":["this is a test error"]}', $genericModel->errors()->toJson());
	}

	public function testSetErrorShouldAddErrorWhenErrorsNotEqualsNull(){
		$genericModel = new Dungeon();

		$genericModel->validate();

		$this->assertNotNull($genericModel->errors());

		$genericModel->setError("testError", "this is a test error");

		$this->assertContains('"testError":["this is a test error"]', $genericModel->errors()->toJson());
	}

	public function testSetRequiredMissingShouldSetRequiredMissingDataFromNullValues(){
		$genericModel = new Dungeon();

		$user = factory(App\User::class)->create();

		$this->actingAs($user);

		$genericModel->setRequiredMissing();

		$this->assertNotNull($genericModel->approved);
		$this->assertNotNull($genericModel->public);
		$this->assertNotNull($genericModel->owner_id);

		$this->assertFalse($genericModel->approved);
		$this->assertFalse($genericModel->public);
		$this->assertEquals($user->id, $genericModel->owner_id);
	}

	public function testDuplicateFoundShouldReturnFalseIfDuplicateIsNotExist(){
		DungeonTrait::truncate();
		$genericTrait = factory(\App\DungeonTrait::class)->make();
		$this->assertFalse($genericTrait->duplicateFound());
	}

	public function testDuplicateFoundShouldReturnTrueIfDuplicateIsFound(){
		$genericTrait = factory(\App\DungeonTrait::class)->make();
		$genericTrait->save();

		$genericTrait2 = factory(\App\DungeonTrait::class)->make();

		$this->assertTrue($genericTrait2->duplicateFound());
	}

	public function testAddCustomRuleShouldAddCustomRule(){

		$dungeonTrait = factory(\App\DungeonTrait::class)->make();

		$this->assertNotContains("test => 'min:0|required'", $dungeonTrait->getRules());

		$dungeonTrait->addCustomRule("test", "min:0|required");

		$this->assertArrayHasKey("test", $dungeonTrait->getRules());
		$this->assertEquals("min:0|required", $dungeonTrait->getRules()['test']);
	}

	public function testSafeSaveShouldReturnFalseAndNotSaveIfValidateFails(){
		$genericModel  = new Dungeon();

		$this->assertNull($genericModel->id);

		$this->assertFalse($genericModel->safeSave());

		$this->assertNull($genericModel->id);

	}

	public function testSafeSaveShouldReturnTrueAndSaveIfValid(){
		$genericModel  = factory(\App\Dungeon::class)->make();

		$this->assertNull($genericModel->id);

		$this->assertTrue($genericModel->safeSave());

		$this->assertNotNull($genericModel->id);

	}

	public function testSafeUpdateShouldReturnTrueAndSaveIfValid(){
		$dungeon  = factory(\App\Dungeon::class)->create();

		$this->assertNotNull($dungeon->id);

		$this->assertEquals("foo", $dungeon->name);

		$dungeon->name = 'new name';

		$this->assertTrue($dungeon->safeUpdate());

		$databaseModel = Dungeon::findById($dungeon->id);
		$this->assertEquals("new name", $databaseModel->name);
	}

	public function testSafeUpdateShouldReturnFalseAndNotUpdateIfInvalid(){
		$dungeon  = factory(\App\Dungeon::class)->create();

		$this->assertNotNull($dungeon->id);

		$this->assertEquals("foo", $dungeon->name);

		$dungeon->name = null;

		$this->assertFalse($dungeon->safeUpdate());

		$databaseModel = Dungeon::findById($dungeon->id);
		$this->assertEquals("foo", $databaseModel->name);
	}

	public function testValidateShouldReturnTrueIfModelIsValid(){
		$dungeon  = factory(\App\Dungeon::class)->make();

		$this->assertTrue($dungeon->validate());
	}

	public function testValidateShouldReturnFalseIfModelIsMissingPublicApprovedOrOwnerId(){
		$dungeon  = factory(\App\Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->public = null;
		$this->assertFalse($dungeon->validate());

		$dungeon  = factory(\App\Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->approved = null;
		$this->assertFalse($dungeon->validate());

		$dungeon  = factory(\App\Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->owner_id = null;
		$this->assertFalse($dungeon->validate());
	}

	public function testValidateShouldReturnTrueIfModelIsMissingPublicApprovedOrOwnerIdAndDefaultValidationRulesGetOverridden(){
		$dungeon  = factory(\App\Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->public = null;
		$this->assertTrue($dungeon->validate(true));

		$dungeon  = factory(\App\Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->approved = null;
		$this->assertTrue($dungeon->validate(true));

		$dungeon  = factory(\App\Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->owner_id = null;
		$this->assertTrue($dungeon->validate(true));
	}

	public function testRunUpdateOrSaveShouldCreateRecordWhenItDoesNotExist(){
		$dungeon  = factory(\App\Dungeon::class)->make();

		$this->assertNull($dungeon->id);

		$dungeon->runUpdateOrSave();

		$this->assertNotNull($dungeon->id);
	}

	public function testRunUpdateOrSaveShouldUpdateRecordWhenItDoesExist(){


		$dungeon  = factory(\App\Dungeon::class)->create();
		$this->assertNotNull($dungeon->id);

		$this->assertEquals("foo", $dungeon->name);

		$dungeon->name = "bar";

		$dungeon->runUpdateOrSave();

		$databaseModel = Dungeon::findById($dungeon->id);
		$this->assertEquals("bar", $databaseModel->name);
	}

	public function testFindByIdShouldReturnModelOfGivenId(){
		$dungeon  = factory(\App\Dungeon::class)->create();
		$this->assertNotNull($dungeon->id);

		$databaseModel = Dungeon::findById($dungeon->id);
		$this->assertNotNull($databaseModel);

		$this->assertEquals($dungeon->id, $databaseModel->id);
		$this->assertEquals($dungeon->name, $databaseModel->name);
	}

	public function testFindByIdShouldReturnNullIfGivenIdNotFound(){
		$databaseModel = Dungeon::findById(78978);
		$this->assertNull($databaseModel);
	}

	public function testSetIfFieldNotPresentShouldRunFunctionIfFieldNotPresent(){
		$dungeon = new Dungeon();

		$this->assertNull($dungeon->name);

		$dungeon->setIfFieldNotPresent("name", function (){
			return "foo";
		});

		$this->assertEquals("foo", $dungeon->name);
	}

	public function testSetIfFieldNotPresentShouldNotRunFunctionIfFieldPresent(){
		$dungeon = new Dungeon();
		$dungeon->name = "foo";

		$this->assertNotNull($dungeon->name);

		$dungeon->setIfFieldNotPresent("name", function (){
			return "bar";
		});

		$this->assertEquals("foo", $dungeon->name);
	}

	public function testSetJsonFromRowIfPresentShouldStripDashes(){
		$dungeon = new Dungeon();

		$row = ['map' => '[[\"x\"]]'];

		$this->assertNull($dungeon->map);

		$dungeon->setJsonFromRowIfPresent("map", $row);

		$this->assertEquals('[["x"]]', $dungeon->map);

	}

	public function testSetJsonFromRowIfPresentShouldUseDafaultIfRowDoesNotContainField(){
		$dungeon = new Dungeon();

		$row = [];

		$this->assertNull($dungeon->map);

		$dungeon->setJsonFromRowIfPresent("map", $row, "[]");

		$this->assertEquals('[]', $dungeon->map);
	}

	public function testGetErrorMessageShouldReturnErrorMessageOfGivenAction(){
		$dungeon = new Dungeon();

		$dungeon->setError("testError", "this is an error message");

		$this->assertEquals('Could not do action: {"testError":["this is an error message"]}', $dungeon->getErrorMessage("do action"));
	}

	public function testGetErrorMessageShouldUseCreateAsActionAndReturnErrorMessageByDefault(){
		$dungeon = new Dungeon();

		$dungeon->setError("testError", "this is an error message");

		$this->assertEquals('Could not save: {"testError":["this is an error message"]}', $dungeon->getErrorMessage());
	}

	public function testGetErrorMessageShouldUseUpdateAsActionWhenItemIsSavedAndReturnErrorMessageByDefault(){
		$dungeon  = factory(\App\Dungeon::class)->create();
		$this->assertNotNull($dungeon->id);

		$dungeon->setError("testError", "this is an error message");

		$this->assertEquals('Could not update: {"testError":["this is an error message"]}', $dungeon->getErrorMessage());
	}

	public function testGetErrorMessageShouldSayNoErrorsPresentWhenNoneExist(){
		$dungeon = new Dungeon();

		$this->assertEquals("No errors present", $dungeon->getErrorMessage());
	}

	public function testAttemptUpdateShouldReturnFalseIfRowDoesNotExist(){
		DungeonTrait::truncate();
		$this->assertFalse(DungeonTrait::attemptUpdate(["id" => 1]));
	}

	public function testAttemptUpdateShouldReturnFalseIfRowHasNoId(){
		DungeonTrait::truncate();
		$this->assertFalse(DungeonTrait::attemptUpdate([]));
	}

	public function testAttemptUpdateShouldReturnUpdateRowIfGivenValidData(){
		DungeonTrait::truncate();

		$dungeonTrait = factory(DungeonTrait::class)->create();

		$this->assertNotEquals("bars", $dungeonTrait->trait);
		$dungeonTrait->trait = "bars";

		$this->assertTrue(DungeonTrait::attemptUpdate($dungeonTrait->toArray()));

		$dbRecord = DungeonTrait::where(GenericModel::ID, $dungeonTrait->id)->first();

		$this->assertEquals($dungeonTrait->id, $dbRecord->id);
		$this->assertEquals("bars", $dbRecord->trait);
	}

	public function testUploadShouldAddDungeonTrait(){
		$user = factory(App\User::class)->create();
		$this->actingAs($user);

		$path = "resources/assets/testing/csv/DungeonTrait/testUpload.csv";
		$uploadFile = new FileTesting($path);

		$this->assertTrue($uploadFile->exists());

		DungeonTrait::truncate();

		$count = count(DungeonTrait::all());

		$message = DungeonTrait::runUpload($path, DungeonTrait::UPLOAD_COLUMNS);

		$this->assertEquals($count+1, count(DungeonTrait::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$trait = DungeonTrait::where("trait", "uploadTestName")->first();

		$this->assertNotNull($trait);

		$this->assertEquals("name", $trait->type);
		$this->assertEquals("uploadTestName", $trait->trait);
	}
}