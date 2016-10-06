<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/5/16
 * Time: 10:11 PM
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetTraitTest extends TestCase
{
	use DatabaseTransactions;

	private $logging;

	public function __construct()
	{
		$this->logging = new \App\Services\Logging(self::class);
		parent::__construct();
	}

	public function testGetRandomByTypeShouldReturnRandomTrait(){
		factory(App\DungeonTrait::class)->create();

		$dungeonTrait = new \App\DungeonTrait();
		$dbTrait = $dungeonTrait->getRandomByType("foo");

		$this->assertEquals("foo", $dbTrait->type);
		$this->assertEquals("bar", $dbTrait->trait);
		$this->assertEquals(1, $dbTrait->weight);
	}

	public function testGetRandomByTypeShouldReturnBlankIfNoRandomTraitFound(){
		factory(App\DungeonTrait::class)->create();

		$dungeonTrait = new \App\DungeonTrait();
		$dbTrait = $dungeonTrait->getRandomByType("foos");

		$this->assertEquals("", $dbTrait);
	}

}