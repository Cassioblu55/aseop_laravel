<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\NonPlayerCharacterTrait;
use App\TestingUtils\FileTesting;

class NonPlayerCharacterTraitTest extends TestCase
{
    private $logging;
    private $user;

	const TEST_UPLOAD_ROW = [
		"trait" => "Sam",
		"type" => "male_name"
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
		$npcTrait = factory(NonPlayerCharacterTrait::class)->make();
		$this->assertTrue($npcTrait->validate());

		$npcTrait->trait = '';
		$this->assertFalse($npcTrait->validate());

		$expectedError = 'Could not save: {"trait":["The trait field is required."]}';
		$this->assertEquals($expectedError, $npcTrait->getErrorMessage());

		$npcTrait->trait = null;
		$this->assertFalse($npcTrait->validate());

		$expectedError = 'Could not save: {"trait":["The trait field is required."]}';
		$this->assertEquals($expectedError, $npcTrait->getErrorMessage());
	}

	public function testValidateShouldFailIfTypeNullOrBlank(){
		$npcTrait = factory(NonPlayerCharacterTrait::class)->make();
		$this->assertTrue($npcTrait->validate());

		$npcTrait->type = '';
		$this->assertFalse($npcTrait->validate());

		$expectedError = 'Could not save: {"type":["The type field is required."]}';
		$this->assertEquals($expectedError, $npcTrait->getErrorMessage());

		$npcTrait->description = null;
		$this->assertFalse($npcTrait->validate());

		$expectedError = 'Could not save: {"type":["The type field is required."]}';
		$this->assertEquals($expectedError, $npcTrait->getErrorMessage());

		$npcTrait->type = 'foo';
		$this->assertFalse($npcTrait->validate());

		$expectedError = 'Could not save: {"type":["The selected type is invalid."]}';
		$this->assertEquals($expectedError, $npcTrait->getErrorMessage());
	}

	public function testDuplicateVillianTraitShouldNotBeValid(){
		$npcTrait = factory(NonPlayerCharacterTrait::class)->create();
		$this->assertTrue($npcTrait->validate());

		$npcTraitTwo = factory(NonPlayerCharacterTrait::class)->make();
		$this->assertFalse($npcTraitTwo->validate());
		$expectedError = 'Could not save: {"duplication_error":["Duplicate object found."]}';
		$this->assertEquals($expectedError, $npcTraitTwo->getErrorMessage());
	}


	public function testUploadShouldAddNonPlayerCharacterTrait(){

		$path = "resources/assets/testing/csv/NonPlayerCharacterTrait/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		NonPlayerCharacterTrait::truncate();

		$count = count(NonPlayerCharacterTrait::all());

		$message = NonPlayerCharacterTrait::upload($path);

		$this->assertEquals($count+1, count(NonPlayerCharacterTrait::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$nonPlayerCharacterTrait = NonPlayerCharacterTrait::where("trait", "Sam")->first();

		$this->assertNotNull($nonPlayerCharacterTrait);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $nonPlayerCharacterTrait->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$nonPlayerCharacterTrait = new NonPlayerCharacterTrait();

		$nonPlayerCharacterTrait->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $nonPlayerCharacterTrait->toArray());
	}

}