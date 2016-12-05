<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\VillainController;
use App\Villain;

class VillainControllerTest extends TestCase
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

		self::ensureNpcOfIdOneExists();
	}

	public function tearDown()
	{
		$this->actingAs(new \App\User());

		App\NonPlayerCharacterTrait::truncate();

		parent::tearDown();
	}


	public function testCreateShouldShowCreateNewObjectPage(){
		$this->callSecure('GET', 'villains/create');

		$this->assertResponseOk();

		$this->assertViewHas('villain');
	}

	public function testEditShouldShowEditObjectPage(){
		$villain = factory(\App\Villain::class)->create();

		$this->callSecure('GET', 'villains/'.$villain->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('villain');
	}

	public function testShowShouldShowShowObjectPage(){
		$villain = factory(\App\Villain::class)->create();

		$this->callSecure('GET', 'villains/'.$villain->id);

		$this->assertResponseOk();

		$this->assertViewHas('villain');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'villains/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){

		factory(\App\Villain::class)->create();

		$response = $this->callSecure('GET', 'api/villains');

		$this->assertResponseOk();

		$villains = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($villains));

		$villain = $villains[0];

		$expectedData = [
			'npc_id' => 1,
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $villain);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewVillain(){
		$villain = [
			'npc_id' => 1,
		];


		$response = $this->call('POST', '/villains', $villain);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/villains/1?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(Villain::all()));

		$storedVillain = Villain::findById(1);
		$this->assertNotNull($storedVillain);

		$this->assertEquals(1, $storedVillain->npc_id);

		$this->assertEquals(0, $storedVillain->approved);
		$this->assertEquals(0, $storedVillain->public);
		$this->assertEquals($this->user->id, $storedVillain->owner_id);
	}

	public function testStoreShouldNotCreateNewVillainWhenVillainInvalid(){
		$villain = [
			'method_type' => 'Bounty hunting or assassination',
			'npc_id' => 1
		];

		$response = $this->call('POST', '/villains', $villain);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/villains/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(Villain::all()));
	}

	public function testUpdateShouldUpdateObject()
	{
		$villain = factory(Villain::class)->create();

		$newVillain = [
			'method_type' => 'Bounty hunting or assassination',
			'method_description' => 'Hire a deadly assassin',
			'id' => $villain->id
		];

		$storedVillain = Villain::findById($villain->id);
		$this->assertNull($storedVillain->method_type);
		$this->assertNull($storedVillain->method_description);

		$response = $this->call('PATCH', 'villains/' . $villain->id, $newVillain);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/villains/1?successMessage=Record+Updated+Successfully'));

		$storedVillain = Villain::findById($villain->id);
		$this->assertEquals('Bounty hunting or assassination', $storedVillain->method_type);
		$this->assertEquals('Hire a deadly assassin', $storedVillain->method_description);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$villain = factory(Villain::class)->create();

		$newVillain = [
			'npc_id' => -1,
			'id' => $villain->id
		];

		$storedVillain = Villain::findById($villain->id);
		$this->assertEquals(1, $storedVillain->npc_id);

		$response = $this->call('PATCH', 'villains/'.$villain->id, $newVillain);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/villains/'.$villain->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedVillain = Villain::findById($villain->id);
		$this->assertEquals(1, $storedVillain->npc_id);
	}

	public function testDestroyShouldDeleteRecord(){
		$villain = factory(Villain::class)->create();

		$count = count(Villain::all());

		$response = $this->call('DELETE', 'villains/'.$villain->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/villains?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(Villain::all()));
	}

}