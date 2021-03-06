<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Spell;
use App\TestingUtils\FileTesting;

class SpellTest extends TestCase
{
    private $logging;
    private $user;

	const TEST_UPLOAD_ROW = [
		"name" => "Bless",
		"type" => "enchantment",
		"class" => "paladin",
		"level" => 1,
		"casting_time" => "1 action",
		"range" => 30,
		"components" => "V, S, M (a sprinkling of holy water)",
		"duration" => 	"Concentration, up to 1 minute",
		"description" =>"You bless up to three creatures of your choice within range. Whenever a target makes an attack roll or a saving throw before the spell ends, the target can roll a d4 and add the number rolled to the attack roll or saving throw.At Higher Levels: When you cast this spell using a spell slot of 2nd level or higher, you can target one additional creature for each slot level above 1st."
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

	public function testValidateShouldFailIfTypeNullOrBlank(){
		$spell = factory(Spell::class)->make();
		$this->assertTrue($spell->validate());

		$spell->type = '';
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"type":["The type field is required."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());

		$spell->type = null;
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"type":["The selected type is invalid.","The type field is required."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());

		$spell->type = 'foo';
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"type":["The selected type is invalid."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());
	}

	public function testValidateShouldFailIfRangeIsNullOrTooLow(){
		$spell = factory(Spell::class)->make();
		$this->assertTrue($spell->validate());

		$spell->range = '';
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"range":["The range field is required."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());

		$spell->range = null;
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"range":["The range field is required."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());

		$spell->range = -1;
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"range":["The range must be at least 0."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());
	}

	public function testValidateShouldFailIfLevelIsNullTooHighOrTooLow(){
		$spell = factory(Spell::class)->make();
		$this->assertTrue($spell->validate());

		$spell->level = '';
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"level":["The level field is required."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());

		$spell->level = null;
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"level":["The level field is required."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());

		$spell->level = -1;
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"level":["The level must be at least 0."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());

		$spell->level = 10;
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"level":["The level may not be greater than 9."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());
	}

	public function testValidateShouldFailIfNameNullOrBlank(){
		$spell = factory(Spell::class)->make();
		$this->assertTrue($spell->validate());

		$spell->name = '';
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"name":["The name field is required."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());

		$spell->name = null;
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"name":["The name field is required."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());
	}

	public function testValidateShouldFailIfNameTaken(){
		$spell = factory(Spell::class)->create();
		$this->assertTrue($spell->validate());

		$spellTwo = factory(Spell::class)->make();
		$this->assertFalse($spellTwo->validate());

		$expectedError = 'Could not save: {"name":["The name has already been taken."]}';
		$this->assertEquals($expectedError, $spellTwo->getErrorMessage());
	}

	public function testValidateShouldFailIfDescriptionNullOrBlank(){
		$spell = factory(Spell::class)->make();
		$this->assertTrue($spell->validate());

		$spell->description = '';
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"description":["The description field is required."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());

		$spell->description = null;
		$this->assertFalse($spell->validate());

		$expectedError = 'Could not save: {"description":["The description field is required."]}';
		$this->assertEquals($expectedError, $spell->getErrorMessage());
	}

	public function testUploadShouldAddSpell(){
		$path = "resources/assets/testing/csv/Spell/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		Spell::truncate();

		$count = count(Spell::all());

		$message = Spell::upload($path);

		$this->assertEquals($count+1, count(Spell::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$spell = Spell::where("name", "Bless")->first();

		$this->assertNotNull($spell);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $spell->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$nonPlayerCharacterTrait = new Spell();

		$nonPlayerCharacterTrait->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $nonPlayerCharacterTrait->toArray());
	}

}