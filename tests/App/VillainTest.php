<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Villain;
use App\TestingUtils\FileTesting;

class VillainTest extends TestCase
{
    private $logging;
	private $user;

	const TEST_UPLOAD_ROW = [
		"npc_id" => 1,
		"method_type" => "Theft or Property Crime",
		"method_description" => "Mugging",
		"scheme_type" => "Mayhem",
		"scheme_description" => "Fulfill an apocalyptic prophecy",
		"weakness_type"=> "Weakness",
		"weakness_description" => "The villains power is broken if the death of its true love is avenged."
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

		$npcTraitsToCreate = [['type'=> 'female_name', 'trait' => 'Jan'],
			['type'=> 'male_name', 'trait' => 'Bob']];

		foreach ($npcTraitsToCreate as $row){
			factory(App\NonPlayerCharacterTrait::class)->create($row);
		}

		self::ensureNpcOfIdOneExists();
	}

	public function tearDown()
	{
		$this->actingAs(new \App\User());
		parent::tearDown();
	}

	public function testValidateShouldFailIfWeaknessTypeOrDescriptionIsMissingAndTheOtherIsPresent(){
		$villain = factory(Villain::class)->make();
		$this->assertTrue($villain->validate());

		$villain->weakness_type = "foo";
		$this->assertFalse($villain->validate());

		$expectedError = 'Could not save: {"weakness_description":["The weakness description field is required when weakness type is present."]}';
		$this->assertEquals($expectedError, $villain->getErrorMessage());

		$villain->weakness_type = null;
		$this->assertTrue($villain->validate());

		$villain->weakness_description = "foo";
		$this->assertFalse($villain->validate());

		$expectedError = 'Could not save: {"weakness_type":["The weakness type field is required when weakness description is present."]}';
		$this->assertEquals($expectedError, $villain->getErrorMessage());
	}

	public function testValidateShouldFailIfSchemeTypeOrDescriptionIsMissingAndTheOtherIsPresent(){
		$villain = factory(Villain::class)->make();
		$this->assertTrue($villain->validate());

		$villain->scheme_type = "foo";
		$this->assertFalse($villain->validate());

		$expectedError = 'Could not save: {"scheme_description":["The scheme description field is required when scheme type is present."]}';
		$this->assertEquals($expectedError, $villain->getErrorMessage());

		$villain->scheme_type = null;
		$this->assertTrue($villain->validate());

		$villain->scheme_description = "foo";
		$this->assertFalse($villain->validate());

		$expectedError = 'Could not save: {"scheme_type":["The scheme type field is required when scheme description is present."]}';
		$this->assertEquals($expectedError, $villain->getErrorMessage());
	}

	public function testValidateShouldFailIfMethodTypeOrDescriptionIsMissingAndTheOtherIsPresent(){
		$villain = factory(Villain::class)->make();
		$this->assertTrue($villain->validate());

		$villain->method_type = "foo";
		$this->assertFalse($villain->validate());

		$expectedError = 'Could not save: {"method_description":["The method description field is required when method type is present."]}';
		$this->assertEquals($expectedError, $villain->getErrorMessage());

		$villain->method_type = null;
		$this->assertTrue($villain->validate());

		$villain->method_description = "foo";
		$this->assertFalse($villain->validate());

		$expectedError = 'Could not save: {"method_type":["The method type field is required when method description is present."]}';
		$this->assertEquals($expectedError, $villain->getErrorMessage());
	}

	public function testGenerateShouldGenerateValidVilain(){

		$villain = Villain::generate();
		$this->assertTrue($villain->validate());

		$this->assertNotNull($villain->npc());

		$this->assertNotNull(\App\NonPlayerCharacter::findById($villain->npc_id));
	}

	public function testGenerateShouldNotSetATypeIfDecriptionDoesNotExistOrViseVersa(){
		factory(\App\VillainTrait::class)->make([
			'type' => "weakness_type",
		]);

		$villain = Villain::generate();
		$this->assertNotNull($villain->id);
		$this->assertNull(($villain->weakness_type));

		\App\VillainTrait::truncate();

		factory(\App\VillainTrait::class)->make([
			'type' => "weakness_description",
		]);

		$villain = Villain::generate();
		$this->assertNotNull($villain->id);
		$this->assertNull(($villain->weakness_description));
	}


	public function testUploadShouldAddVillain(){

		$path = "resources/assets/testing/csv/Villain/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		Villain::truncate();

		$count = count(Villain::all());

		$message = Villain::upload($path);

		$this->assertEquals($count+1, count(Villain::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$villain = Villain::where("method_type", "Theft or Property Crime")->first();

		$this->assertNotNull($villain);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $villain->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$villain = new Villain();

		$villain->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $villain->toArray());
	}

}