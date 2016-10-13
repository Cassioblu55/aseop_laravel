<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\NonPlayerCharacter;
use App\NonPlayerCharacterTrait;
use App\TestingUtils\FileTesting;

class NonPlayerCharacterTest extends TestCase
{
    private $logging;
	private $user;

	const TEST_UPLOAD_ROW = [
		"first_name" => "Maxwell",
		"last_name" => "Logan",
		"age" => 24,
		"sex" => "M",
		"height" => 64,
		"weight" => 173,
		"flaw" =>"Arrogance",
		"interaction" => "Quiet",
		"mannerism" => "Prone to predictions of doom",
		"bond" => "Out for revenge",
		"appearance" => "Unusual hair color",
		"talent" => "Can sing beautifully",
		"ideal"	 => "Independence",
		"ability" => "Looks scrawny"
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

	public function testGenerateShouldNotCreateAndSaveValidNPCIfNoNamesAreAviable(){
		$npc = NonPlayerCharacter::generate();
		$this->assertNull($npc->id);
	}

    public function testGenerateShouldCreateAndSaveValidNPC(){

    	$traitsToCreate = [
    		['type'=> 'female_name', 'trait' => 'Jan'],
		    ['type'=> 'male_name', 'trait' => 'Bob'],
		    ['type'=> 'last_name', 'trait' => 'Hudson'],
		    ['type'=> 'flaw', 'trait' => 'Bad breath'],
		    ['type'=> 'interaction', 'trait' => 'interaction'],
		    ['type'=> 'appearance', 'trait' => 'hot'],
		    ['type'=> 'mannerism', 'trait' => 'posh'],
		    ['type'=> 'ability', 'trait' => 'can just really high'],
		    ['type'=> 'ideal', 'trait' => 'art'],
		    ['type'=> 'talent', 'trait' => 'great singer']
	    ];

	    foreach ($traitsToCreate as $trait){
	        factory(NonPlayerCharacterTrait::class)->create($trait);
	    }

    	$npc = NonPlayerCharacter::generate();
	    $this->assertNotNull($npc->id);

	    $this->logging->logError($npc->sex);
	    $this->assertTrue(($npc->sex == "M" || $npc->sex == "F"));

	    $expectedName = ($npc->sex == "M") ? "Bob" : "Jan";
	    $this->assertEquals($expectedName, $npc->first_name);

	    $this->assertEquals("Hudson", $npc->last_name);
	    $this->assertEquals("Bad breath", $npc->flaw);
	    $this->assertEquals("interaction", $npc->interaction);
	    $this->assertEquals("hot", $npc->appearance);
	    $this->assertEquals("posh", $npc->mannerism);
	    $this->assertEquals("can just really high", $npc->ability);
	    $this->assertEquals("art", $npc->ideal);
	    $this->assertEquals("great singer", $npc->talent);

	    $this->assertGreaterThanOrEqual(0, $npc->age);
	    $this->assertGreaterThanOrEqual(0, $npc->height);
	    $this->assertGreaterThanOrEqual(0, $npc->wieght);

	    $this->assertFalse($npc->public);
	    $this->assertFalse($npc->approved);
	    $this->assertEquals($this->user->id, $npc->owner_id);
    }

	public function testUploadShouldAddNonPlayerCharacter(){
		$user = factory(App\User::class)->create();
		$this->actingAs($user);

		$path = "resources/assets/testing/csv/NonPlayerCharacter/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		NonPlayerCharacter::truncate();

		$count = count(NonPlayerCharacter::all());

		$message = NonPlayerCharacter::upload($path);

		$this->assertEquals($count+1, count(NonPlayerCharacter::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$npc = NonPlayerCharacter::where("first_name", "Maxwell")->first();

		$this->assertNotNull($npc);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $npc->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$npc = new NonPlayerCharacter();

		$npc->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $npc->toArray());
	}

}