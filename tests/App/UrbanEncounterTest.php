<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\UrbanEncounter;
use App\TestingUtils\FileTesting;

class UrbanEncounterTest extends TestCase
{
    private $logging;
	private $user;

	const TEST_UPLOAD_ROW = [
		"title" => "Animals on the Loose",
		"description" => "The characters see one or more unexpected animals loose in the street. This challenge could be anything from a pack of baboons to an escaped circus bear, tiger, or elephants.",
		"rolls" => "1d5+2"
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

		self::ensureNpcOfIdOneExists();
	}

	public function tearDown()
	{
		$this->actingAs(new \App\User());
		parent::tearDown();
	}

	public function testUploadShouldAddUrbanEncounter(){

		$path = "resources/assets/testing/csv/UrbanEncounter/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		UrbanEncounter::truncate();

		$count = count(UrbanEncounter::all());

		$message = UrbanEncounter::upload($path);

		$this->assertEquals($count+1, count(UrbanEncounter::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$urbanEncounter = UrbanEncounter::where("title", "Animals on the Loose")->first();

		$this->assertNotNull($urbanEncounter);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $urbanEncounter->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$urbanEncounter = new UrbanEncounter();

		$urbanEncounter->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $urbanEncounter->toArray());
	}

}