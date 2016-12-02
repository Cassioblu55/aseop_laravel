<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\DungeonController;
use Illuminate\Http\Request;

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

	public function testEditShouldShowEditObjectPage(){
		$dungeon = factory(\App\Dungeon::class)->create();

		$this->callSecure('GET', 'dungeons/'.$dungeon->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('dungeon');
	}

	public function testShowShouldShowShowObjectPage(){
		$dungeon = factory(\App\Dungeon::class)->create();

		$this->callSecure('GET', 'dungeons/'.$dungeon->id);

		$this->assertResponseOk();

		$this->assertViewHas('dungeon');
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
		factory(\App\Dungeon::class)->create();

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

}