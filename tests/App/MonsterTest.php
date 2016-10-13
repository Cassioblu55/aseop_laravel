<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Monster;

class MonsterTest extends TestCase
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

	public function testSetUploadValuesShouldSetUploadValues()
	{
		$row = [
			"name" => "Worg",
			"hit_points" => "4d10+4",
			"stats" => '{"strength":16,"dexterity":13,"constitution":13,"intelligence":7,"wisdom":11,"charisma":8}',
			"languages" => '[{"language":"Worg"},{"language":"Goblin"}]',
			"skills" => '[{"skill":"Perception","modifier":4}]',
			"challenge" => 0.50,
			"abilities" => '[{"description":"The worg has advantage on Wisdom (Perception) checks that rely on hearing or smell.","name":"Keen Hearing and Smell"}]',
			"actions" => '[{"description":"Melee Weapon Attack: +5 to hit, reach 5 ft ., one target. Hit: 10 (2d6 + 3) piercing damage. If the target is a creature, it must succeed on a DC 13 Strength saving throw or be knocked prone.","name":"Bite"}]',
			"found" => '[{"found":"Hills"},{"found":"Forest"},{"found":"Plains"},{"found":"Underground"}]',
			"senses" => '[{"sense":"darkvision 60 ft"},{"sense":"passive Perception 14"}]',
			"description" => "A worg is an evil predator that delights in hunting and devouring creatures weaker than itself. Cunning and malevolent, worgs roam across the remote wilderness or a re raised by goblins and hobgoblins. Those creatures use worgs as mounts, but a worg will turn on its rider if it feels mistreated or malnourished. Worgs speak in their own language and Goblin, and a few learn to speak Common as well.",
			"speed" => 50,
			"armor" => 13,
			"xp" => 100
		];

		$monster = new Monster();

		$monster->setUploadValues($row);

		$this->assertHashesHaveEqualValues($row, $monster->toArray());
	}

    public function testValidateShouldFailIfStatsIsInvalid(){
    	$monster = factory(Monster::class)->create();

	    $this->assertTrue($monster->validate());

	    $monster->stats = '{"invalid":"stat"}';

	    $this->assertFalse($monster->validate());

    	$expectedErrorMessage = '{"stats":["Stats invalid."]}';

	    $this->assertEquals($expectedErrorMessage, $monster->getErrorsJson());
    }

	public function testValidateShouldFailIfHitPointsIsInvalid(){
		$monster = factory(Monster::class)->create();

		$this->assertTrue($monster->validate());

		$monster->hit_points = 'invalid hit points';

		$this->assertFalse($monster->validate());

		$expectedErrorMessage = '{"hit_points":["Hit Points invalid."]}';

		$this->assertEquals($expectedErrorMessage, $monster->getErrorsJson());
	}

	public function testValidateShouldFaillIfAbilitiesInvalid(){
		$monster = factory(Monster::class)->create();
		$this->assertTrue($monster->validate());

		$monster->{Monster::ABILITIES} = null;
		$this->assertTrue($monster->validate());

		$monster->{Monster::ABILITIES} = "";
		$this->assertTrue($monster->validate());

		$monster->{Monster::ABILITIES} = '[{"name":"Rejuvenation","description":"If it dies, the naga returns to life in 1d6 days and regains all its hit points. Only a wish spell can prevent this trait from functioning."}]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::ABILITIES} = '[]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::ABILITIES} = '[{"name":"Rejuvenation"}]';
		$this->assertFalse($monster->validate());

		$expectedErrorMessage = '{"abilities":["Abilities invalid."]}';
		$this->assertEquals($expectedErrorMessage, $monster->getErrorsJson());

		$monster->{Monster::ABILITIES} = 'invalid abilities';
		$this->assertFalse($monster->validate());

		$monster->{Monster::ABILITIES} = '{"name":"Rejuvenation","description":"If it dies, the naga returns to life in 1d6 days and regains all its hit points. Only a wish spell can prevent this trait from functioning."}';
		$this->assertFalse($monster->validate());
	}

	public function testValidateShouldFaillIfFoundInvalid(){
		$monster = factory(Monster::class)->create();
		$this->assertTrue($monster->validate());

		$monster->{Monster::FOUND} = null;
		$this->assertTrue($monster->validate());

		$monster->{Monster::FOUND} = "";
		$this->assertTrue($monster->validate());

		$monster->{Monster::FOUND} = '[{"found":"Desert"}]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::FOUND} = '[]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::FOUND} = '[{}]';
		$this->assertFalse($monster->validate());

		$expectedErrorMessage = '{"found":["Found invalid."]}';
		$this->assertEquals($expectedErrorMessage, $monster->getErrorsJson());

		$monster->{Monster::FOUND} = 'invalid found';
		$this->assertFalse($monster->validate());

		$monster->{Monster::FOUND} = '{"found":"Desert"}';
		$this->assertFalse($monster->validate());
	}

	public function testValidateShouldFaillIfSensesInvalid(){
		$monster = factory(Monster::class)->create();
		$this->assertTrue($monster->validate());

		$monster->{Monster::SENSES} = null;
		$this->assertTrue($monster->validate());

		$monster->{Monster::SENSES} = "";
		$this->assertTrue($monster->validate());

		$monster->{Monster::SENSES} = '[{"sense":"darkvision 60ft"}]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::SENSES} = '[]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::SENSES} = '[{}]';
		$this->assertFalse($monster->validate());

		$expectedErrorMessage = '{"senses":["Senses invalid."]}';
		$this->assertEquals($expectedErrorMessage, $monster->getErrorsJson());

		$monster->{Monster::SENSES} = 'invalid sense';
		$this->assertFalse($monster->validate());

		$monster->{Monster::SENSES} = '{"sense":"darkvision 60ft"}';
		$this->assertFalse($monster->validate());
	}

	public function testValidateShouldFaillIfLanguagesInvalid(){
		$monster = factory(Monster::class)->create();
		$this->assertTrue($monster->validate());

		$monster->{Monster::LANGUAGES} = null;
		$this->assertTrue($monster->validate());

		$monster->{Monster::LANGUAGES} = "";
		$this->assertTrue($monster->validate());

		$monster->{Monster::LANGUAGES} = '[{"language":"Worg"}]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::LANGUAGES} = '[]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::LANGUAGES} = '[{}]';
		$this->assertFalse($monster->validate());

		$expectedErrorMessage = '{"languages":["Languages invalid."]}';
		$this->assertEquals($expectedErrorMessage, $monster->getErrorsJson());

		$monster->{Monster::LANGUAGES} = 'invalid sense';
		$this->assertFalse($monster->validate());

		$monster->{Monster::LANGUAGES} = '{"languages":"Worg"}';
		$this->assertFalse($monster->validate());
	}

	public function testValidateShouldFaillIfActionsInvalid(){
		$monster = factory(Monster::class)->create();
		$this->assertTrue($monster->validate());

		$monster->{Monster::ACTIONS} = null;
		$this->assertTrue($monster->validate());

		$monster->{Monster::ACTIONS} = "";
		$this->assertTrue($monster->validate());

		$monster->{Monster::ACTIONS} = '[{"description":"Melee or Ranged Weapon Attack: +3 to hit, reach 5 ft. or range 20f60 ft., one target. Hit: 4 (1d6 + 1) piercing damage.","name":"Spear"}]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::ACTIONS} = '[]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::ACTIONS} = '[{"name":"Spear"}]';
		$this->assertFalse($monster->validate());

		$expectedErrorMessage = '{"actions":["Actions invalid."]}';
		$this->assertEquals($expectedErrorMessage, $monster->getErrorsJson());

		$monster->{Monster::ACTIONS} = 'invalid action';
		$this->assertFalse($monster->validate());

		$monster->{Monster::ACTIONS} = '{"description":"Melee or Ranged Weapon Attack: +3 to hit, reach 5 ft. or range 20f60 ft., one target. Hit: 4 (1d6 + 1) piercing damage.","name":"Spear"}';
		$this->assertFalse($monster->validate());
	}

	public function testValidateShouldFaillIfSkillsInvalid(){
		$monster = factory(Monster::class)->create();
		$this->assertTrue($monster->validate());

		$monster->{Monster::SKILLS} = null;
		$this->assertTrue($monster->validate());

		$monster->{Monster::SKILLS} = "";
		$this->assertTrue($monster->validate());

		$monster->{Monster::SKILLS} = '[{"skill":"Perception","modifier":2}]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::SKILLS} = '[]';
		$this->assertTrue($monster->validate());

		$monster->{Monster::SKILLS} = '[{"skill":"Perception"}]';
		$this->assertFalse($monster->validate());

		$expectedErrorMessage = '{"skills":["Skills invalid."]}';
		$this->assertEquals($expectedErrorMessage, $monster->getErrorsJson());

		$monster->{Monster::SKILLS} = 'invalid action';
		$this->assertFalse($monster->validate());

		$monster->{Monster::SKILLS} = '[{"skill":"Perception","modifier":-1}]';
		$this->assertFalse($monster->validate());

		$monster->{Monster::SKILLS} = '[{"skill":"Perception","modifier":"foo bar"}]';
		$this->assertFalse($monster->validate());

		$monster->{Monster::SKILLS} = '{"description":"Melee or Ranged Weapon Attack: +3 to hit, reach 5 ft. or range 20f60 ft., one target. Hit: 4 (1d6 + 1) piercing damage.","name":"Spear"}';
		$this->assertFalse($monster->validate());
	}

}