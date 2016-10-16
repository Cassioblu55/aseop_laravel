<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Tavern;
use App\TestingUtils\FileTesting;
use \App\NonPlayerCharacter;

class TavernTest extends TestCase
{
    private $logging;
    private $user;

	const TEST_UPLOAD_ROW = [
		"name" => "The Gleaming Horde",
		"type" => "Raucous dive",
		"tavern_owner_id" => 1
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

	public function testValidateShouldFailIfNameNullOrBlank(){
		$tavern = factory(Tavern::class)->make();
		$this->assertTrue($tavern->validate());

		$tavern->name = '';
		$this->assertFalse($tavern->validate());

		$expectedError = 'Could not save: {"name":["The name field is required."]}';
		$this->assertEquals($expectedError, $tavern->getErrorMessage());

		$tavern->name = null;
		$this->assertFalse($tavern->validate());

		$expectedError = 'Could not save: {"name":["The name field is required."]}';
		$this->assertEquals($expectedError, $tavern->getErrorMessage());
	}

	public function testValidateShouldFailIfTypeNullOrBlank(){
		$tavern = factory(Tavern::class)->make();
		$this->assertTrue($tavern->validate());

		$tavern->type = '';
		$this->assertFalse($tavern->validate());

		$expectedError = 'Could not save: {"type":["The type field is required."]}';
		$this->assertEquals($expectedError, $tavern->getErrorMessage());

		$tavern->type = null;
		$this->assertFalse($tavern->validate());

		$expectedError = 'Could not save: {"type":["The type field is required."]}';
		$this->assertEquals($expectedError, $tavern->getErrorMessage());
	}

	public function testValidateShouldFailIfTavernOwnerNotExistingInNpcTableOrBlank(){
		$tavern = factory(Tavern::class)->make();
		$this->assertTrue($tavern->validate());

		NonPlayerCharacter::truncate();
		$this->assertFalse($tavern->validate());

		$expectedError = 'Could not save: {"tavern_owner_id":["The selected tavern owner id is invalid."]}';
		$this->assertEquals($expectedError, $tavern->getErrorMessage());;
	}

	public function testGenerateShouldCreateTavern(){
		$npcCount = count(NonPlayerCharacter::all());

		$tavernTraitsToCreate = [
			['trait' => 'Rat', 'type' => 'last_name'],
			['trait' => 'The Red', 'type' => 'first_name'],
			['trait' => 'Black Tie Only', 'type' => 'type']
		];

		foreach ($tavernTraitsToCreate as $row){
			factory(\App\TavernTrait::class)->create($row);
		}

		$tavern = Tavern::generate();
		$this->assertTrue($tavern->validate());

		$this->assertEquals($npcCount+1, count(NonPlayerCharacter::all()));
	}

	public function testGenerateShouldNotCreateNpcIfTavernCouldNotBeCreated(){
		$npcCount = count(NonPlayerCharacter::all());

		$tavern = Tavern::generate();
		$this->assertFalse($tavern->validate());

		$this->assertEquals($npcCount, count(NonPlayerCharacter::all()));
	}


	public function testUploadShouldAddTavern(){
		$path = "resources/assets/testing/csv/Tavern/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		Tavern::truncate();

		$count = count(Tavern::all());

		$message = Tavern::upload($path);

		$this->assertEquals($count+1, count(Tavern::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$tavern = Tavern::where("name", "The Gleaming Horde")->first();

		$this->assertNotNull($tavern);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $tavern->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$tavern = new Tavern();

		$tavern->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $tavern->toArray());
	}

}