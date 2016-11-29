<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Trap;
use App\TestingUtils\FileTesting;

class TrapTest extends TestCase
{
    private $logging;
    private $user;

	const TEST_UPLOAD_ROW = [
		"name" => "uploadTrap",
		"description" => "foo",
		"rolls" => '1d6+2,2d6+6'
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

	public function testValidateShouldFailIfNameNullOrBlank(){
		$trap = factory(Trap::class)->make();
		$this->assertTrue($trap->validate());

		$trap->name = '';
		$this->assertFalse($trap->validate());

		$expectedError = 'Could not save: {"name":["The name field is required."]}';
		$this->assertEquals($expectedError, $trap->getErrorMessage());

		$trap->name = null;
		$this->assertFalse($trap->validate());

		$expectedError = 'Could not save: {"name":["The name field is required."]}';
		$this->assertEquals($expectedError, $trap->getErrorMessage());
	}

	public function testValidateShouldFailIfDescriptionNullOrBlank(){
		$trap = factory(Trap::class)->make();
		$this->assertTrue($trap->validate());

		$trap->description = '';
		$this->assertFalse($trap->validate());

		$expectedError = 'Could not save: {"description":["The description field is required."]}';
		$this->assertEquals($expectedError, $trap->getErrorMessage());

		$trap->description = null;
		$this->assertFalse($trap->validate());

		$expectedError = 'Could not save: {"description":["The description field is required."]}';
		$this->assertEquals($expectedError, $trap->getErrorMessage());
	}

	public function testUploadShouldAddTrap(){

		$path = "resources/assets/testing/csv/Trap/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		Trap::truncate();

		$count = count(Trap::all());

		$message = Trap::upload($path);

		$this->assertEquals($count+1, count(Trap::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$trap = Trap::where("name", "uploadTrap")->first();

		$this->assertNotNull($trap);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $trap->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$trap = new Trap();

		$trap->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $trap->toArray());
	}

	public function testRollsShouldFailValidationIfInvalid(){
		$trap = factory(Trap::class)->make();
		$this->assertTrue($trap->validate());

		$trap->rolls = '1d6-4';
		$this->assertTrue($trap->validate());

		$trap->rolls = 'foobar';
		$this->assertFalse($trap->validate());

		$expectedError = 'Could not save: {"rolls":["Rolls invalid."]}';
		$this->assertEquals($expectedError, $trap->getErrorMessage());

		$trap->rolls = '1e6+4';
		$this->assertFalse($trap->validate());

		$expectedError = 'Could not save: {"rolls":["Rolls invalid."]}';
		$this->assertEquals($expectedError, $trap->getErrorMessage());

		$trap->rolls = '1d6+4,1r5+5';
		$this->assertFalse($trap->validate());

		$expectedError = 'Could not save: {"rolls":["Rolls invalid."]}';
		$this->assertEquals($expectedError, $trap->getErrorMessage());
	}

}