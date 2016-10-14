<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Settlement;
use App\NonPlayerCharacter;
use App\SettlementTrait;
use App\TestingUtils\FileTesting;
use App\NonPlayerCharacterTrait;

class SettlementTest extends TestCase
{
    private $logging;
    private $user;

	const TEST_UPLOAD_ROW = [
		"name" => "Humphrey",
		"known_for" => "Hordes of beggars",
		"notable_traits" => "Awful smell (tanneries open sewers)",
		"ruler_status" => "Respected fair and just",
		"current_calamity" => "Important figure died (murder suspected)",
		"population" => 67,
		"size" => "S",
		"race_relations" => "Tension or rivalry",
		"ruler_id" => 1
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

	    factory(NonPlayerCharacterTrait::class)->create(
		    ["type" => "male_name", "trait" => "Bill"]
	    );

	    factory(NonPlayerCharacterTrait::class)->create(
		    ["type" => "female_name", "trait" => "Jan"]
	    );
    }

    public function tearDown()
    {
        $this->actingAs(new \App\User());
	    NonPlayerCharacterTrait::truncate();

        parent::tearDown();
    }

    public function testGenerateShouldCreateValidSettlement(){
	    SettlementTrait::truncate();

	    $traitsToCreate = [
		    ['type'=> 'name', 'trait' => 'Cassioburg'],
		    ['type'=> 'known_for', 'trait' => 'Having too much money'],
		    ['type'=> 'notable_traits', 'trait' => 'Lots of water in the streets'],
		    ['type'=> 'current_calamity', 'trait' => 'End of the world']
	    ];

	    foreach ($traitsToCreate as $trait){
		    factory(SettlementTrait::class)->create($trait);
	    }

	    $npcCount = count(NonPlayerCharacter::all());

		$settlement = Settlement::generate();

	    $this->assertEquals($npcCount+1, count(NonPlayerCharacter::all()));
	    $this->assertNotNull($settlement->id);

	    $this->assertGreaterThanOrEqual(0, $settlement->population);
	    $this->assertContains($settlement->size, ["L", "M", "S"]);

	    $this->assertEquals("Cassioburg", $settlement->name);
	    $this->assertEquals("Having too much money", $settlement->known_for);
	    $this->assertEquals("Lots of water in the streets", $settlement->notable_traits);
	    $this->assertEquals("End of the world", $settlement->current_calamity);


	    $this->assertFalse($settlement->public);
	    $this->assertFalse($settlement->approved);
	    $this->assertEquals($this->user->id, $settlement->owner_id);
    }

	public function testGenerateShouldNotCreateNPCIfNoSettlementNamesArePresent(){
		NonPlayerCharacter::truncate();
		$npcCount = count(NonPlayerCharacter::all());

		$settlement = Settlement::generate();
		$this->assertNull($settlement->id);

		$this->assertEquals($npcCount, count(NonPlayerCharacter::all()));
	}

	public function testUploadShouldAddSettlement(){
		NonPlayerCharacter::truncate();
		$npc = factory(NonPlayerCharacter::class)->create();
		if($npc->id != 1){
			$npc->id = 1;
			$npc->runUpdateOrSave();
		}

		$path = "resources/assets/testing/csv/Settlement/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		Settlement::truncate();

		$count = count(Settlement::all());

		$message = Settlement::upload($path);

		$this->assertEquals($count+1, count(Settlement::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$settlement = Settlement::where("name", "Humphrey")->first();

		$this->assertNotNull($settlement);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $settlement->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$settlement = new Settlement();

		$settlement->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $settlement->toArray());
	}

}