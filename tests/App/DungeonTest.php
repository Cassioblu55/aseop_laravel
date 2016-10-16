<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/5/16
 * Time: 10:34 PM
 */

use App\Dungeon;

class DungeonTest extends TestCase
{
	private $logging;
	private $user;
	private $dungeon;

	public function __construct()
	{
		$this->logging = new \App\Services\Logging(self::class);
		parent::__construct();
	}

	public function setUp(){
		parent::setUp();

		$this->user = factory(\App\User::class)->create();
		$this->actingAs($this->user);

		self::ensureTrapOfIdOneExists();

		$this->dungeon = factory(Dungeon::class)->make();
		$this->assertTrue($this->dungeon->validate());

	}

	public function tearDown()
	{
		$this->actingAs(new \App\User());
		parent::tearDown();
	}

	public function testValidateShouldFailIfDungeonHasNoName(){
		$this->dungeon->name = null;
		$this->assertFalse($this->dungeon->validate());

		$this->dungeon = factory(Dungeon::class)->make();
		$this->assertTrue($this->dungeon->validate());

		$this->dungeon->name = "";
		$this->assertFalse($this->dungeon->validate());

		$expectedError = 'Could not save: {"name":["The name field is required."]}';
		$this->assertEquals($expectedError, $this->dungeon->getErrorMessage());
	}

	public function testValidateShouldFailIfDungeonSizeIsInvalid(){
		$this->dungeon->size="f";

		$this->assertFalse($this->dungeon->validate());

		$expectedError = 'Could not save: {"size":["The selected size is invalid."]}';
		$this->assertEquals($expectedError, $this->dungeon->getErrorMessage());
	}

	public function testGetMapSizeIntegerShouldReturnNullIfSizeIsInvalid(){
		$this->dungeon->size = "f";

		$this->assertNull($this->dungeon->getMapSizeInteger());
	}

	public function testGetNumberOfTrapsInMapShouldReturnTheNumberOfTraps(){
		$this->assertEquals(2, $this->dungeon->getNumberOfTrapsInMap());

		$this->dungeon->map = null;

		$this->assertNull($this->dungeon->getNumberOfTrapsInMap());
	}


	public function testValidateShouldFailIfMapIsInvalidSize(){
		$this->dungeon->map = '{"map":"This is not a valid map."}';
		$this->assertFalse($this->dungeon->validate());

		$expectedError = 'Could not save: {"map":["Map in incorrect size, map size: 1, should be: 8."]}';
		$this->assertEquals($expectedError, $this->dungeon->getErrorMessage());

		$this->dungeon = factory(Dungeon::class)->make();
		$this->assertTrue($this->dungeon->validate());

		$this->dungeon->map = '[["w", "w"], ["w", "w"]]';
		$this->assertFalse($this->dungeon->validate());

		$expectedError = 'Could not save: {"map":["Map in incorrect size, map size: 2, should be: 8."]}';
		$this->assertEquals($expectedError, $this->dungeon->getErrorMessage());
	}

	public function testValidateShouldFailIfRowIsNotArray(){
		$this->dungeon->size = "S";
		$this->dungeon->map = '[["w","w","w","s","w","w"],["x","w","x","w","x","w"],["w","w","w","w","w","w"],"foo bar",["w","x","w","w","w","x"],["x","x","x","w","x","w"]]';
		$this->assertFalse($this->dungeon->validate());

		$expectedError = 'Could not save: {"map":["Map row 3 invalid."]}';
		$this->assertEquals($expectedError, $this->dungeon->getErrorMessage());
	}

	public function testValidateShouldFailIfMapIsNotSquare(){
		$this->dungeon->size = "S";
		$this->dungeon->map = '[["w","s","w","w","w","w"],["x","w","x","w","x","w"],["w","w","w","w","w","w"],["x","w","x","x","x"],["w","x","w","w","w","x"],["x","x","x","w","x","w"]]';
		$this->assertFalse($this->dungeon->validate());

		$expectedError = 'Could not save: {"map":["Map is not square. Row 3 has size 5 instead of 6."]}';
		$this->assertEquals($expectedError, $this->dungeon->getErrorMessage());
	}

	public function testValidateShouldFailIfCotainsInvalidCharacter(){
		$this->dungeon->size = "S";
		$this->dungeon->map = '[["w","s","w","w","w","w"],["x","f","x","w","x","w"],["w","w","w","w","w","w"],["x","w","x","x","x","x"],["w","x","w","w","w","x"],["x","x","x","w","x","w"]]';
		$this->assertFalse($this->dungeon->validate());

		$expectedError = 'Could not save: {"map":["Map contains invalid square. \'f\' invalid, row 1 column B."]}';
		$this->assertEquals($expectedError, $this->dungeon->getErrorMessage());
	}

	public function testValidateShouldFailIfNoStartExists(){
		$this->dungeon->size = "S";
		$this->dungeon->map = '[["w","w","w","w","w","w"],["x","w","x","w","x","w"],["w","w","w","w","w","w"],["x","w","x","x","x","x"],["w","x","w","w","w","x"],["x","x","x","w","x","w"]]';
		$this->assertFalse($this->dungeon->validate());

		$expectedError = 'Could not save: {"map":["Map contains no start."]}';
		$this->assertEquals($expectedError, $this->dungeon->getErrorMessage());
	}

	public function testValidateShouldFailIfExtraTrapsExist(){
		$this->dungeon->map = '[["w","w","w","s","w","w","t","w"],["x","w","x","w","x","w","x","w"],["t","w","w","w","w","t","w","w"],["w","x","x","w","x","w","x","x"],["w","w","w","x","w","w","w","x"],["w","x","x","x","x","w","x","w"],["w","x","t","w","w","w","w","w"],["x","x","x","x","x","t","x","x"]]';
		$this->dungeon->validate();
		$this->assertFalse($this->dungeon->validate());
		$this->logging->logInfo($this->dungeon->getErrorMessage());

		$expectedError = 'Could not save: {"map":["Map has traps marked not saved in traps."]}';
		$this->assertEquals($expectedError, $this->dungeon->getErrorMessage());
	}

	public function testGenerateShouldReturnIncompleteDungeonWithNoMapOrTraps(){

		factory(App\DungeonTrait::class)->create();

		$dungeon = Dungeon::generate();

		$this->assertNull($dungeon->map);
		$this->assertEquals("[]", $dungeon->traps);
		$this->assertNotEmpty($dungeon->name);
		$this->assertEquals("bar", $dungeon->name);

		$this->assertContains($dungeon->size, ['S', 'M', 'L']);
	}

	public function testUploadShouldUploadFileFromPath(){

		$path = "resources/assets/testing/csv/Dungeon/testingUploadFile_DO_NOT_EDIT.csv";
		TestCase::assertFileExists($path);

		$count = count(Dungeon::all());

		$message = Dungeon::upload($path);

		$this->assertEquals($count+1, count(Dungeon::all()));
		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$dungeon = Dungeon::where("name", "uploadTestName")->first();
		$this->assertNotNull($dungeon);

		$uploadMap = '[["w","w","w","s","w","w","w","w"],["x","w","x","w","x","w","x","w"],["w","w","w","w","w","w","w","w"],["w","x","x","w","x","w","x","x"],["w","w","w","x","w","w","w","x"],["w","x","x","x","x","w","x","w"],["w","x","t","w","w","w","w","w"],["x","x","x","x","x","t","x","x"]]';
		$this->assertEquals($uploadMap, $dungeon->map);

		$uploadTraps = '[["1","6","2"],["1","7","5"]]';
		$this->assertEquals($uploadTraps, $dungeon->traps);

		$this->assertEquals("M", $dungeon->size);
	}

	public function testGetMapSquareShouldReturnNullIfMapInvalid(){
		$dungeon = factory(Dungeon::class)->make();

		$dungeon->map = "invalid";

		$this->assertNull($dungeon->getMapSquare(0,0));
	}

	public function testGetMapSquareShouldReturnRowValue(){
		$this->assertEquals("w", $this->dungeon->getMapSquare(0,0));

		$this->assertEquals("s", $this->dungeon->getMapSquare(0,3));

		$this->assertEquals("t", $this->dungeon->getMapSquare(0,6));
	}

	public function testUploadShouldNotUploadIfDataMalformed(){
		$path = "resources/assets/testing/csv/Dungeon/testingUploadFileFailed_DO_NOT_EDIT.csv";
		TestCase::assertFileExists($path);

		$count = count(Dungeon::all());

		$message = Dungeon::upload($path);

		$this->assertEquals($count, count(Dungeon::all()));
		$this->assertEquals("0 records added 0 updated 1 records could not be uploaded", $message);
	}

	public function testValidateShouldPassIfTrapsEmptyNullOrBlank(){
		$dungeon = factory(Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->traps = "";
		$this->assertTrue($dungeon->validate());

		$dungeon->traps = null;
		$this->assertTrue($dungeon->validate());

		$dungeon->traps = "[]";
		$this->assertTrue($dungeon->validate());
	}

	public function testValidateShouldFailIfDungeonTrapInvalid(){
		$dungeon = factory(Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->traps = "this in an invalid trap";
		$this->assertFalse($dungeon->validate());

		$expectedError = 'Could not save: {"traps":["Traps invalid."]}';
		$this->assertEquals($expectedError, $dungeon->getErrorMessage());
	}

	public function testValidateShouldFailIfTrapDoesNotExistInDatabase(){
		$dungeon = factory(Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->traps = '[["1","0","6"],["2","2","0"]]';
		$this->assertFalse($dungeon->validate());

		$expectedError = 'Could not save: {"traps":["Trap number 2 not found in database."]}';
		$this->assertEquals($expectedError, $dungeon->getErrorMessage());
	}

	public function testValidateShouldFailIfTrapNotArray(){
		$dungeon = factory(Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->traps = '[["1","0","6"],"foobar"]';
		$this->assertFalse($dungeon->validate());

		$expectedError = 'Could not save: {"traps":["Trap number 2 invalid, not array."]}';
		$this->assertEquals($expectedError, $dungeon->getErrorMessage());
	}

	public function testValidateShouldFailIfTrapTooSmall(){
		$dungeon = factory(Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->traps = '[["1","0","6"],["1","2"]]';
		$this->assertFalse($dungeon->validate());

		$expectedError = 'Could not save: {"traps":["Trap number 2 invalid, too small."]}';
		$this->assertEquals($expectedError, $dungeon->getErrorMessage());
	}

	public function testValidateShouldFailIfTrapDoesntContainAllNumbers(){
		$dungeon = factory(Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->traps = '[["1","2","e"], ["1","2","2"]]';
		$this->assertFalse($dungeon->validate());

		$expectedError = 'Could not save: {"traps":["Trap number 1 invalid, \'e\' not an integer."]}';
		$this->assertEquals($expectedError, $dungeon->getErrorMessage());
	}

	public function testDownloadShouldReturnArrayOfDungeons(){
		$filename = "file_".date("Y-m-d");

		$data = ["filename" => $filename,"title"=> $filename,"excel" => ["allowedProperties" => ["creator","lastModifiedBy","description","subject","keywords","category","manager","company"]],"writer"=> null,"parser"=> null,"ext"=>"xls","storagePath"=>"exports","filesystem"=>new stdClass(),"identifier"=>["filesystem" => new stdClass()]];

		$dungeonsFile = Dungeon::download("file");

		$this->assertEquals(json_encode($data), json_encode($dungeonsFile));
	}
}