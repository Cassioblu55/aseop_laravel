<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/5/16
 * Time: 10:11 PM
 */


class AssetTraitTest extends TestCase
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

	public function testGetRandomByTypeShouldReturnRandomTrait(){


		factory(App\DungeonTrait::class)->create();

		$dungeonTrait = new \App\DungeonTrait();
		$dbTrait = $dungeonTrait->getRandomByType("name");

		$this->assertNotNull($dbTrait->id);
	}

	public function testGetRandomByTypeShouldReturnBlankIfNoRandomTraitFound(){


		factory(App\DungeonTrait::class)->create();

		$dungeonTrait = new \App\DungeonTrait();
		$dbTrait = $dungeonTrait->getRandomByType("foos");

		$this->assertEquals("", $dbTrait);
	}

}