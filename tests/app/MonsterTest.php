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

		$expectedErrorMessage = '{"hit_points":["Hit points invalid."]}';

		$this->assertEquals($expectedErrorMessage, $monster->getErrorsJson());
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

}