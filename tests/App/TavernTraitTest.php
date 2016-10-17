<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\TavernTrait;
use App\TestingUtils\FileTesting;

class TavernTraitTest extends TestCase
{
    private $logging;
	private $user;

	const TEST_UPLOAD_ROW = [
		"trait" => "Eel",
		"type" => "last_name"
	];

	public function __construct()
	{
		$this->logging = new \App\Services\Logging(self::class);
		parent::__construct();
	}

	public function setUp(){
		parent::setUp();

		$this->user = factory(\App\User::class)->create();
		$this->actingAs($this->user);

	}

	public function tearDown()
	{
		$this->actingAs(new \App\User());
		parent::tearDown();
	}

	public function testValidateShouldFailIfTraitNullOrBlank(){
		$tavernTrait = factory(TavernTrait::class)->make();
		$this->assertTrue($tavernTrait->validate());

		$tavernTrait->trait = '';
		$this->assertFalse($tavernTrait->validate());

		$expectedError = 'Could not save: {"trait":["The trait field is required."]}';
		$this->assertEquals($expectedError, $tavernTrait->getErrorMessage());

		$tavernTrait->trait = null;
		$this->assertFalse($tavernTrait->validate());

		$expectedError = 'Could not save: {"trait":["The trait field is required."]}';
		$this->assertEquals($expectedError, $tavernTrait->getErrorMessage());
	}

	public function testValidateShouldFailIfTypeNullOrBlank(){
		$tavernTrait = factory(TavernTrait::class)->make();
		$this->assertTrue($tavernTrait->validate());

		$tavernTrait->type = '';
		$this->assertFalse($tavernTrait->validate());

		$expectedError = 'Could not save: {"type":["The type field is required."]}';
		$this->assertEquals($expectedError, $tavernTrait->getErrorMessage());

		$tavernTrait->type = null;
		$this->assertFalse($tavernTrait->validate());

		$expectedError = 'Could not save: {"type":["The selected type is invalid.","The type field is required."]}';
		$this->assertEquals($expectedError, $tavernTrait->getErrorMessage());

		$tavernTrait->type = 'foo';
		$this->assertFalse($tavernTrait->validate());

		$expectedError = 'Could not save: {"type":["The selected type is invalid."]}';
		$this->assertEquals($expectedError, $tavernTrait->getErrorMessage());
	}

	public function testDuplicateVillianTraitShouldNotBeValid(){
		$tavernTrait = factory(TavernTrait::class)->create();
		$this->assertTrue($tavernTrait->validate());

		$tavernTraitTwo = factory(TavernTrait::class)->make();
		$this->assertFalse($tavernTraitTwo->validate());

		$expectedError = 'Could not save: {"duplication_error":["Duplicate object found."]}';
		$this->assertEquals($expectedError, $tavernTraitTwo->getErrorMessage());
	}

	public function testUploadShouldAddTavernTrait(){

		$path = "resources/assets/testing/csv/TavernTrait/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		TavernTrait::truncate();

		$count = count(TavernTrait::all());

		$message = TavernTrait::upload($path);

		$this->assertEquals($count+1, count(TavernTrait::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$tavernTrait = TavernTrait::where("trait", "Eel")->first();

		$this->assertNotNull($tavernTrait);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $tavernTrait->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$tavernTrait = new TavernTrait();

		$tavernTrait->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $tavernTrait->toArray());
	}

}