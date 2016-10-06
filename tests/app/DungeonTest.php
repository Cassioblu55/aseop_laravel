<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/5/16
 * Time: 10:34 PM
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Dungeon;

class DungeonTest extends TestCase
{
	use DatabaseTransactions;

	const RULES = [
		'name' =>'required|max:255',
		'map' => 'required|json',
		'traps' => 'required|json',
		'size' => 'in:S,M,L|required|size:1'
	];

	private $logging;

	public function __construct()
	{
		$this->logging = new \App\Services\Logging(self::class);
		parent::__construct();
	}

	public function testAllRequiredRulesPresent(){
		$dungeon = new Dungeon();
		$this->assertEquals(self::RULES, $dungeon->getRules());
	}

	public function testGenerateShouldReturnIncompleteDungeonWithNoMapOrTraps(){
		$user = factory(App\User::class)->create();
		$this->actingAs($user);

		$dungeonName = factory(App\DungeonTrait::class)->make();
		$dungeonName->type = "name";
		$dungeonName->runUpdateOrSave();

		$dungeon = Dungeon::generate();

		$this->assertNull($dungeon->map);
		$this->assertEquals("[]", $dungeon->traps);
		$this->assertNotEmpty($dungeon->name);
		$this->assertContains($dungeon->size, ['S', 'M', 'L']);
	}

	public function testUploadShouldUploadFileFromPath(){
		$user = factory(App\User::class)->create();
		$this->actingAs($user);

		$path = "resources/assets/csv/Dungeon/testingUploadFile_DO_NOT_EDIT.csv";
		TestCase::assertFileExists($path);

		$count = count(Dungeon::all());

		$message = Dungeon::upload($path);

		$this->assertEquals($count+1, count(Dungeon::all()));
		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$dungeon = Dungeon::where("name", "uploadTestName")->first();
		$this->assertNotNull($dungeon);

		$uploadMap = "[[\"w\",\"w\",\"w\",\"s\",\"w\",\"w\",\"w\",\"w\"],[\"x\",\"w\",\"x\",\"w\",\"x\",\"w\",\"x\",\"w\"],[\"w\",\"w\",\"w\",\"w\",\"w\",\"w\",\"w\",\"w\"],[\"w\",\"x\",\"x\",\"w\",\"x\",\"w\",\"x\",\"x\"],[\"w\",\"w\",\"w\",\"x\",\"w\",\"w\",\"w\",\"x\"],[\"w\",\"x\",\"x\",\"x\",\"x\",\"w\",\"x\",\"w\"],[\"w\",\"x\",\"t\",\"w\",\"w\",\"w\",\"w\",\"w\"],[\"x\",\"x\",\"x\",\"x\",\"x\",\"t\",\"x\",\"x\"]]";
		$this->assertEquals($uploadMap, $dungeon->map);

		$uploadTraps = "[[\"3\",\"1\",\"4\"],[\"3\",\"7\",\"0\"]]";
		$this->assertEquals($uploadTraps, $dungeon->traps);

		$this->assertEquals("L", $dungeon->size);
	}

	public function testUploadShouldNotUploadIfDataMalformed(){
		$user = factory(App\User::class)->create();

		$this->actingAs($user);

		$path = "resources/assets/csv/Dungeon/testingUploadFileFailed_DO_NOT_EDIT.csv";
		TestCase::assertFileExists($path);

		$count = count(Dungeon::all());

		$message = Dungeon::upload($path);

		$this->assertEquals($count, count(Dungeon::all()));
		$this->assertEquals("0 records added 0 updated 1 records could not be uploaded", $message);
	}

	public function testDownloadShouldReturnArrayOfDungeons(){
		$filename = "file_".date("Y-m-d");

		$data = ["filename" => $filename,"title"=> $filename,"excel" => ["allowedProperties" => ["creator","lastModifiedBy","description","subject","keywords","category","manager","company"]],"writer"=> null,"parser"=> null,"ext"=>"xls","storagePath"=>"exports","filesystem"=>new stdClass(),"identifier"=>["filesystem" => new stdClass()]];

		$dungeonsFile = Dungeon::download("file");

		$this->assertEquals(json_encode($data), json_encode($dungeonsFile));
	}

}