<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\ForestEncounter;

class ForestEncounterTest extends TestCase
{
    private $logging;

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

	    $row = ["title" => 'foo', "description" => "bar", "rolls" => "1d6+1"];

	    $forestEncounter->setUploadValues($row);

	    $this->assertNotNull($forestEncounter->id);
	    $this->assertEquals("foo", $forestEncounter->title);
	    $this->assertEquals("bar", $forestEncounter->description);
	    $this->assertEquals("1d6+1", $forestEncounter->rolls);
	    $this->assertEquals($this->user->id, $forestEncounter->owner_id);
	    $this->assertFalse($forestEncounter->approved);

	    $this->assertEquals($count+1, count(ForestEncounter::all()));
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