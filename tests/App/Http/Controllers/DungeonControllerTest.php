<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\DungeonController;

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

	public function testApiShouldRetrunAllData(){
		factory(\App\Dungeon::class)->create();

		$response = $this->callSecure('GET', 'api/dungeons');

		$this->assertResponseOk();

		$this->assertEquals(1, count(json_decode($response->getContent())));
	}

}