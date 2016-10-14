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

		self::ensureNpcOfIdOneExists();
	}

	public function tearDown()
	{
		$this->actingAs(new \App\User());
		parent::tearDown();
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