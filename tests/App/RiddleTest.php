<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Riddle;
use App\TestingUtils\FileTesting;

class RiddleTest extends TestCase
{
    private $logging;
    private $user;

	const TEST_UPLOAD_ROW = [
		"name"=> "uploadRiddle",
		"riddle" => "upload riddle",
		"solution" => "upload riddle solution",
		"hint" => "upload riddle hint"
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
		$riddle = factory(Riddle::class)->make();
		$this->assertTrue($riddle->validate());

		$riddle->name = '';
		$this->assertFalse($riddle->validate());

		$expectedError = 'Could not save: {"name":["The name field is required."]}';
		$this->assertEquals($expectedError, $riddle->getErrorMessage());

		$riddle->name = null;
		$this->assertFalse($riddle->validate());

		$expectedError = 'Could not save: {"name":["The name field is required."]}';
		$this->assertEquals($expectedError, $riddle->getErrorMessage());
	}

	public function testValidateShouldFailIfRiddleNullOrBlank(){
		$riddle = factory(Riddle::class)->make();
		$this->assertTrue($riddle->validate());

		$riddle->riddle = '';
		$this->assertFalse($riddle->validate());

		$expectedError = 'Could not save: {"riddle":["The riddle field is required."]}';
		$this->assertEquals($expectedError, $riddle->getErrorMessage());

		$riddle->riddle = null;
		$this->assertFalse($riddle->validate());

		$expectedError = 'Could not save: {"riddle":["The riddle field is required."]}';
		$this->assertEquals($expectedError, $riddle->getErrorMessage());
	}

	public function testValidateShouldFailIfSolutionNullOrBlank(){
		$riddle = factory(Riddle::class)->make();
		$this->assertTrue($riddle->validate());

		$riddle->solution = '';
		$this->assertFalse($riddle->validate());

		$expectedError = 'Could not save: {"solution":["The solution field is required."]}';
		$this->assertEquals($expectedError, $riddle->getErrorMessage());

		$riddle->solution = null;
		$this->assertFalse($riddle->validate());

		$expectedError = 'Could not save: {"solution":["The solution field is required."]}';
		$this->assertEquals($expectedError, $riddle->getErrorMessage());
	}

    public function testUploadShouldAddRiddleFromFilePath(){
	    $path = "resources/assets/testing/csv/Riddle/testUpload_DO_NOT_EDIT.csv";
	    new FileTesting($path);

	    Riddle::truncate();

	    $count = count(Riddle::all());

	    $message = Riddle::upload($path);

	    $this->assertEquals($count+1, count(Riddle::all()));

	    $this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

	    $npc = Riddle::where("name", "uploadRiddle")->first();

	    $this->assertNotNull($npc);

	    $this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $npc->toArray());
    }

    public function testSetUploadValuesShouldSetValuesOfRow(){
    	$riddle = new Riddle();
	    $riddle->setUploadValues(self::TEST_UPLOAD_ROW);

	    $this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $riddle);
    }

}