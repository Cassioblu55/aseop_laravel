<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\DungeonController;
use Illuminate\Http\Request;
use App\Dungeon;

class DungeonControllerTest extends TestCase
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


	public function testCreateShouldShowCreateNewObjectPage(){
	    $this->callSecure('GET', 'dungeons/create');

	    $this->assertResponseOk();

	    $this->assertViewHas('dungeon');
    }

    public function testStoreShouldCreateNewDungeon(){
	    self::ensureTrapOfIdOneExists();

	    $dungeon = [
	    	'name' => "foo",
		    'map' => '[["w","w","w","s","w","w","t","w"],["x","w","x","w","x","w","x","w"],["t","w","w","w","w","w","w","w"],["w","x","x","w","x","w","x","x"],["w","w","w","x","w","w","w","x"],["w","x","x","x","x","w","x","w"],["w","x","w","w","w","w","w","w"],["x","x","x","x","x","w","x","x"]]',
		    'traps' => '[["1","6","0"],["1","0","2"]]',
		    'size' => 'M'
	    ];

	    $response = $this->call('POST', '/dungeons', $dungeon);

	    $this->assertEquals(302, $response->status());
	    $this->assertRedirectedTo(url('/dungeons/1?successMessage=Record+Added+Successfully'));

	    $this->assertEquals(1, count(Dungeon::all()));

	    $storedDungeon = Dungeon::findById(1);
	    $this->assertNotNull($storedDungeon);

	    $this->assertEquals($dungeon['map'], $storedDungeon->map);
	    $this->assertEquals($dungeon['traps'], $storedDungeon->traps);
	    $this->assertEquals("M", $storedDungeon->size);

	    $this->assertEquals(0, $storedDungeon->approved);
	    $this->assertEquals(0, $storedDungeon->public);
	    $this->assertEquals($this->user->id, $storedDungeon->owner_id);
    }

	public function testStoreShouldNotCreateNewDungeonWhenDungeonInvalid(){
		self::ensureTrapOfIdOneExists();

		$dungeon = [
			'map' => '[["w","w","w","s","w","w","t","w"],["x","w","x","w","x","w","x","w"],["t","w","w","w","w","w","w","w"],["w","x","x","w","x","w","x","x"],["w","w","w","x","w","w","w","x"],["w","x","x","x","x","w","x","w"],["w","x","w","w","w","w","w","w"],["x","x","x","x","x","w","x","x"]]',
			'traps' => '[["1","6","0"],["1","0","2"]]',
			'size' => 'M'
		];

		$response = $this->call('POST', '/dungeons', $dungeon);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/dungeons/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(Dungeon::all()));
	}

	public function testEditShouldShowEditObjectPage(){
		self::ensureTrapOfIdOneExists();

		$dungeon = factory(\App\Dungeon::class)->create();

		$this->callSecure('GET', 'dungeons/'.$dungeon->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('dungeon');
		$this->assertViewHas('headers');
	}

	public function testUpdateShouldUpdateObject(){
		self::ensureTrapOfIdOneExists();

		$dungeon = factory(Dungeon::class)->create();

		$newDungeon = [
			'name' => "This is the new Name",
			'id' => $dungeon->id
		];

		$storedDungeon = Dungeon::findById($dungeon->id);
		$this->assertEquals("foo", $storedDungeon->name);

		$response = $this->call('PATCH', 'dungeons/'.$dungeon->id, $newDungeon);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/dungeons/'.$dungeon->id.'?successMessage=Record+Updated+Successfully'));

		$storedDungeon = Dungeon::findById($dungeon->id);
		$this->assertEquals("This is the new Name", $storedDungeon->name);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		self::ensureTrapOfIdOneExists();

		$dungeon = factory(Dungeon::class)->create();

		$newDungeon = [
			'name' => null,
			'id' => $dungeon->id
		];

		$storedDungeon = Dungeon::findById($dungeon->id);
		$this->assertEquals("foo", $storedDungeon->name);

		$response = $this->call('PATCH', 'dungeons/'.$dungeon->id, $newDungeon);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/dungeons/'.$dungeon->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedDungeon = Dungeon::findById($dungeon->id);
		$this->assertEquals("foo", $storedDungeon->name);
	}

	public function testShowShouldShowShowObjectPage(){
		$dungeon = factory(Dungeon::class)->create();

		$this->callSecure('GET', 'dungeons/'.$dungeon->id);

		$this->assertResponseOk();

		$this->assertViewHas('dungeon');
		$this->assertViewHas('headers');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'dungeons/upload');

		$this->assertResponseOk();
	}

	public function testGenerateWithMapAndTrapsCreatedShouldCreateDungeonWithRequestDataOfADungeonMapAndTraps(){
		self::ensureTrapOfIdOneExists();
		factory(App\DungeonTrait::class)->create();

		$mapAndTraps = [
			'map' => '[["w","w","w","s","w","w","t","w"],["x","w","x","w","x","w","x","w"],["t","w","w","w","w","w","w","w"],["w","x","x","w","x","w","x","x"],["w","w","w","x","w","w","w","x"],["w","x","x","x","x","w","x","w"],["w","x","w","w","w","w","w","w"],["x","x","x","x","x","w","x","x"]]',
			'traps' => '[["1","6","0"],["1","0","2"]]',
			'size' => 'M'
		];

		$request = new Request($mapAndTraps);

		$controller = new DungeonController();

		$view = $controller->generateWithMapAndTrapsCreated($request);

		$dungeons = \App\Dungeon::all();
		$this->assertEquals(1, count($dungeons));

		$newDungeon = $dungeons[0];
		$this->assertEquals($mapAndTraps['map'], $newDungeon->map);
		$this->assertEquals($mapAndTraps['traps'], $newDungeon->traps);

		$this->assertEquals(url('dungeons/1?successMessage=Record+Added+Successfully'), $view->getTargetUrl());
	}

	public function testApiShouldRetrunAllData(){
		factory(Dungeon::class)->create();

		$response = $this->callSecure('GET', 'api/dungeons');

		$this->assertResponseOk();

		$dungeons = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($dungeons));

		$dungeon = $dungeons[0];

		$expectedData = [
			'name' => "foo",
			'map' => '[["w","w","w","s","w","w","t","w"],["x","w","x","w","x","w","x","w"],["t","w","w","w","w","w","w","w"],["w","x","x","w","x","w","x","x"],["w","w","w","x","w","w","w","x"],["w","x","x","x","x","w","x","w"],["w","x","w","w","w","w","w","w"],["x","x","x","x","x","w","x","x"]]',
			'traps' => '[["1","6","0"],["1","0","2"]]',
			'size' => 'M',

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $dungeon);
	}

	public function testDestroyShouldDeleteRecord(){
		$dungeon = factory(Dungeon::class)->create();

		$count = count(Dungeon::all());

		$response = $this->call('DELETE', 'dungeons/'.$dungeon->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/dungeons?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(Dungeon::all()));
	}
}