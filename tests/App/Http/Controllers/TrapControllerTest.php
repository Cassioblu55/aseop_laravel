<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\TrapController;

class TrapControllerTest extends TestCase
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
		$this->callSecure('GET', 'traps/create');

		$this->assertResponseOk();

		$this->assertViewHas('trap');
	}

	public function testEditShouldShowEditObjectPage(){
		$trap = factory(\App\Trap::class)->create();

		$this->callSecure('GET', 'traps/'.$trap->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('trap');
	}

	public function testShowShouldShowShowObjectPage(){
		$trap = factory(\App\Trap::class)->create();

		$this->callSecure('GET', 'traps/'.$trap->id);

		$this->assertResponseOk();

		$this->assertViewHas('trap');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'traps/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\Trap::class)->create();

		$response = $this->callSecure('GET', 'api/traps');

		$this->assertResponseOk();

		$traps = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($traps));

		$trap = $traps[0];

		$expectedData = [
			"name" => "Fire Trap",
			"description" => "If a weight of 50lbs is placed on this title it will activate. Doing 2d6+5 fire damage.",

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $trap);
	}

}