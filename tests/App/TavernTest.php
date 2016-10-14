<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Tavern;
use App\TestingUtils\FileTesting;

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

		self::ensureNpcOfIdOneExists();
	}

	public function tearDown()
	{
		$this->actingAs(new \App\User());
		parent::tearDown();
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