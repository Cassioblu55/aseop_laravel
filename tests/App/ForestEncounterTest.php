<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\ForestEncounter;
use App\TestingUtils\FileTesting;

class ForestEncounterTest extends TestCase
{
    private $logging;

	const TEST_UPLOAD_ROW = [
		"title" => "testUpload",
		"description" => "foobar",
		"rolls" => "2d4+4,5d4+3"
	];

	private $user;

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


    public function testSetUploadValuesShouldAddNeededValuesBasedOnRowData(){
    	$count = count(ForestEncounter::all());

	    $forestEncounter = new ForestEncounter();
	    $this->assertNull($forestEncounter->id);

	    $row = self::TEST_UPLOAD_ROW;

	    $forestEncounter->setUploadValues($row);

	    $this->assertNotNull($forestEncounter->id);
	    $this->assertEquals("testUpload", $forestEncounter->title);
	    $this->assertEquals("foobar", $forestEncounter->description);
	    $this->assertEquals("2d4+4,5d4+3", $forestEncounter->rolls);
	    $this->assertEquals($this->user->id, $forestEncounter->owner_id);
	    $this->assertFalse($forestEncounter->approved);

	    $this->assertEquals($count+1, count(ForestEncounter::all()));
    }

	public function testUploadShouldAddMonster(){
		$user = factory(App\User::class)->create();
		$this->actingAs($user);

		$path = "resources/assets/testing/csv/ForestEncounter/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		ForestEncounter::truncate();

		$count = count(ForestEncounter::all());

		$message = ForestEncounter::upload($path);

		$this->assertEquals($count+1, count(ForestEncounter::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$forestEncounter = ForestEncounter::where("title", "testUpload")->first();

		$this->assertNotNull($forestEncounter);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $forestEncounter->toArray());
	}

	public function testValidateShouldReturnFalseIfRollsIsInvalidAndTrueIfBlank(){
		$forestEncounter = factory(ForestEncounter::class)->make();

		$this->assertTrue($forestEncounter->validate());

		$forestEncounter->rolls = "";

		$this->assertTrue($forestEncounter->validate());

		$forestEncounter->rolls = "this is not a valid roll";

		$this->assertFalse($forestEncounter->validate());

		$expectedErrors = '{"rolls":["Roll string invalid"]}';
		$this->assertEquals($expectedErrors, $forestEncounter->getErrorsJson());
	}

}