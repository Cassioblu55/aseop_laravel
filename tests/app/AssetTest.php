<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/5/16
 * Time: 10:11 PM
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetTest extends TestCase
{
	use DatabaseTransactions;

	private $logging;

	public function __construct()
	{
		$this->logging = new \App\Services\Logging(self::class);
		parent::__construct();
	}

	public function testGetTraitRandomByTypeShouldReturnRandomTrait(){
		$dungeonTrait = factory(App\DungeonTrait::class)->create();
		$this->assertNotNull($dungeonTrait->id);

		$dungeon = new \App\Dungeon();
		$trait = $dungeon->getTraitRandomByType("foo");

		$this->logging->ping();
		$this->logging->logInfo(json_encode($trait));

		$this->assertEquals("bar", $trait);
	}

	public function testGetTraitRandomByTypeShouldReturnMultipleColumnsIfRequested(){
		$dungeonTrait = factory(App\VillainTrait::class)->create();
		$this->assertNotNull($dungeonTrait->id);

		$dungeon = new \App\Villain();
		$trait = $dungeon->getTraitRandomByType("foo", ['description', 'kind']);

		$this->logging->ping();
		$this->logging->logInfo(json_encode($trait));

		$this->assertEquals("description", $trait['description']);
		$this->assertEquals("kind", $trait['kind']);
	}

}