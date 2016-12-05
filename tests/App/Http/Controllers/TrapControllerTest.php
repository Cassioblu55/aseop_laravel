<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\TrapController;
use App\Trap;

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

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewTrap(){
		$trap = [
			"name" => "Fire Trap",
			"description" => "If a weight of 50lbs is placed on this title it will activate. Doing 2d6+5 fire damage."
		];


		$response = $this->call('POST', '/traps', $trap);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/traps?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(Trap::all()));

		$storedTrap = Trap::findById(1);
		$this->assertNotNull($storedTrap);

		$this->assertEquals('Fire Trap', $storedTrap->name);
		$this->assertEquals('If a weight of 50lbs is placed on this title it will activate. Doing 2d6+5 fire damage.', $storedTrap->description);

		$this->assertEquals(0, $storedTrap->approved);
		$this->assertEquals(0, $storedTrap->public);
		$this->assertEquals($this->user->id, $storedTrap->owner_id);
	}

	public function testStoreShouldNotCreateNewTrapWhenTrapInvalid(){
		$trap = [
			"description" => "If a weight of 50lbs is placed on this title it will activate. Doing 2d6+5 fire damage."
		];

		$response = $this->call('POST', '/traps', $trap);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/traps/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(Trap::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$trap = factory(Trap::class)->create();

		$newTrap = [
			"name" => "Ice Trap",
			'id' => $trap->id
		];

		$storedTrap = Trap::findById($trap->id);
		$this->assertEquals("Fire Trap", $storedTrap->name);

		$response = $this->call('PATCH', 'traps/'.$trap->id, $newTrap);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/traps?successMessage=Record+Updated+Successfully'));

		$storedTrap = Trap::findById($trap->id);
		$this->assertEquals("Ice Trap", $storedTrap->name);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$trap = factory(Trap::class)->create();

		$newTrap = [
			'name' => null,
			'id' => $trap->id
		];

		$storedTrap = Trap::findById($trap->id);
		$this->assertEquals("Fire Trap", $storedTrap->name);

		$response = $this->call('PATCH', 'traps/'.$trap->id, $newTrap);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/traps/'.$trap->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedTrap = Trap::findById($trap->id);
		$this->assertEquals("Fire Trap", $storedTrap->name);
	}

	public function testDestroyShouldDeleteRecord(){
		$trap = factory(Trap::class)->create();

		$count = count(Trap::all());

		$response = $this->call('DELETE', 'traps/'.$trap->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/traps?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(Trap::all()));
	}

}