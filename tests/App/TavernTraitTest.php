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