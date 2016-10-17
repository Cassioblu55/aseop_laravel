<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\VillainTrait;
use App\TestingUtils\FileTesting;

class VillainTraitTest extends TestCase
{
    private $logging;
	private $user;


	const TEST_UPLOAD_ROW = [
		"type" => "weakness",
		"kind" => "Hidden Object",
		"description" => "A hidden object holds the villains soul."
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

	public function testValidateShouldFailIfTypeIsIncorrect(){
		$villainTrait = factory(VillainTrait::class)->make();
		$this->assertTrue($villainTrait->validate());

		$villainTrait->type = '';
		$this->assertFalse($villainTrait->validate());

		$expectedError = 'Could not save: {"type":["The type field is required."]}';
		$this->assertEquals($expectedError, $villainTrait->getErrorMessage());

		$villainTrait->type = null;
		$this->assertFalse($villainTrait->validate());

		$expectedError = 'Could not save: {"type":["The selected type is invalid.","The type field is required."]}';
		$this->assertEquals($expectedError, $villainTrait->getErrorMessage());

		$villainTrait->type = 'foo';
		$this->assertFalse($villainTrait->validate());

		$expectedError = 'Could not save: {"type":["The selected type is invalid."]}';
		$this->assertEquals($expectedError, $villainTrait->getErrorMessage());
	}

	public function testValidateShouldFailIfKindIsIncorrect(){
		$villainTrait = factory(VillainTrait::class)->make();
		$this->assertTrue($villainTrait->validate());

		$villainTrait->kind = '';
		$this->assertFalse($villainTrait->validate());

		$expectedError = 'Could not save: {"kind":["The kind field is required."]}';
		$this->assertEquals($expectedError, $villainTrait->getErrorMessage());

		$villainTrait->kind = null;
		$this->assertFalse($villainTrait->validate());

		$expectedError = 'Could not save: {"kind":["The selected kind is invalid.","The kind field is required."]}';
		$this->assertEquals($expectedError, $villainTrait->getErrorMessage());

		$villainTrait->kind = 'foo';
		$this->assertFalse($villainTrait->validate());

		$expectedError = 'Could not save: {"kind":["The selected kind is invalid."]}';
		$this->assertEquals($expectedError, $villainTrait->getErrorMessage());
	}

	public function testDuplicateVillianTraitShouldNotBeValid(){
		$villainTrait = factory(VillainTrait::class)->create();
		$this->assertTrue($villainTrait->validate());

		$villainTraitTwo = factory(VillainTrait::class)->make();
		$this->assertFalse($villainTraitTwo->validate());

		$expectedError = 'Could not save: {"duplication_error":["Duplicate object found."]}';
		$this->assertEquals($expectedError, $villainTraitTwo->getErrorMessage());
	}

	public function testUploadShouldAddVillainTrait()
	{

		$path = "resources/assets/testing/csv/VillainTrait/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		VillainTrait::truncate();

		$count = count(VillainTrait::all());

		$message = VillainTrait::upload($path);

		$this->assertEquals($count + 1, count(VillainTrait::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$villainTrait = VillainTrait::where("kind", "Hidden Object")->first();

		$this->assertNotNull($villainTrait);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $villainTrait->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$villainTrait = new VillainTrait();

		$villainTrait->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $villainTrait->toArray());
	}

}