<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\MonsterController;

class MonsterControllerTest extends TestCase
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
		$this->callSecure('GET', 'monsters/create');

		$this->assertResponseOk();

		$this->assertViewHas('monster');
	}

	public function testEditShouldShowEditObjectPage(){
		$monster = factory(\App\Monster::class)->create();

		$this->callSecure('GET', 'monsters/'.$monster->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('monster');
	}

	public function testShowShouldShowShowObjectPage(){
		$monster = factory(\App\Monster::class)->create();

		$this->callSecure('GET', 'monsters/'.$monster->id);

		$this->assertResponseOk();

		$this->assertViewHas('monster');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'monsters/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\Monster::class)->create();

		$response = $this->callSecure('GET', 'api/monsters');

		$this->assertResponseOk();

		$monsters = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($monsters));

		$monster = $monsters[0];

		$expectedData = [
			"armor" => 10,
			"hit_points" => "1d5+6",
			"speed" => 30,
			"xp" => 20,
			"challenge" => 1.5,
			"name" => "foo monster",
			"stats" => '{"strength":16,"dexterity":13,"constitution":13,"intelligence":7,"wisdom":11,"charisma":8}',

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $monster);
	}

}