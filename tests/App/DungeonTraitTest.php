<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/6/16
 * Time: 11:43 AM
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\DungeonTrait;
use App\TestingUtils\FileTesting;

class DungeonTraitTest extends TestCase
{
	private $logging;

	public function __construct()
	{
		$this->logging = new \App\Services\Logging(self::class);
		parent::__construct();
	}

	public function setUp(){
		parent::setUp();

		$user = factory(\App\User::class)->create();
		$this->actingAs($user);
	}

	public function tearDown()
	{
		$this->actingAs(new \App\User());
		parent::tearDown();
	}


	public function testDungeonTraitRulesAreAsExpected(){
		$expectedRules = [
			"trait" => "required",
			"type" => "in:name,purpose,history,location,creator|required|max:255"
		];

		$dungeonTrait = new DungeonTrait();
		$this->assertEquals($expectedRules, $dungeonTrait->getRules());
	}

	public function testUploadShouldAddDungeonTrait(){
		$user = factory(App\User::class)->create();
		$this->actingAs($user);

		$path = "resources/assets/testing/csv/DungeonTrait/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		DungeonTrait::truncate();

		$count = count(DungeonTrait::all());

		$message = DungeonTrait::upload($path);

		$this->assertEquals($count+1, count(DungeonTrait::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$trait = DungeonTrait::where("trait", "uploadTestName")->first();

		$this->assertNotNull($trait);

		$this->assertEquals("name", $trait->type);
		$this->assertEquals("uploadTestName", $trait->trait);
	}

	public function testDungeonTraitsThatMatchOneAlreadyInDatabaceWillNotPassValidation(){
		$this->assertEquals(0, count(DungeonTrait::all()));

		$dungeonTrait = factory(App\DungeonTrait::class)->make();
		$this->assertTrue($dungeonTrait->validate());

		$dungeonTrait->runUpdateOrSave();
		$this->assertNotNull($dungeonTrait->id);

		$newDungeonTrait = factory(DungeonTrait::class)->make();
		$this->assertFalse($newDungeonTrait->validate());
	}

	public function testValidTraitTypesEqualDungeonValidTraitTypes(){
		$this->assertEquals(\App\Dungeon::FILLABLE_FROM_TRAIT_TABLE, DungeonTrait::getValidTraitTypes());
	}

	public function testGetNewSelfShouldReturnNewDungeonTrait(){
		$dungeonTrait = DungeonTrait::getNewSelf();
		$this->assertEquals('App\DungeonTrait', get_class($dungeonTrait));
	}

	public function testValidateShouldFailIfTraitNull(){
		$dungeonTrait = factory(App\DungeonTrait::class)->make();
		$this->assertTrue($dungeonTrait->validate());

		$dungeonTrait->trait = null;
		$this->assertFalse($dungeonTrait->validate());

		$dungeonTrait->trait = "";
		$this->assertFalse($dungeonTrait->validate());

		$expectedError = 'Could not save: {"trait":["The trait field is required."]}';
		$this->assertEquals($expectedError, $dungeonTrait->getErrorMessage());
	}

	public function testValidateShouldFailIfTypeInvalid()
	{
		$dungeonTrait = factory(App\DungeonTrait::class)->make();
		$this->assertTrue($dungeonTrait->validate());

		$dungeonTrait->type = "foo";
		$this->assertFalse($dungeonTrait->validate());

		$expectedError = 'Could not save: {"type":["The selected type is invalid."]}';
		$this->assertEquals($expectedError, $dungeonTrait->getErrorMessage());
	}

}