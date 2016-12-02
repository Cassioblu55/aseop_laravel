<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\ForestEncounterController;
use App\ForestEncounter;

class ForestEncounterControllerTest extends TestCase
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
		$this->callSecure('GET', 'forestEncounters/create');

		$this->assertResponseOk();

		$this->assertViewHas('forestEncounter');
	}

	public function testEditShouldShowEditObjectPage(){
		$forestEncounter = factory(\App\ForestEncounter::class)->create();

		$this->callSecure('GET', 'forestEncounters/'.$forestEncounter->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('forestEncounter');
	}

	public function testShowShouldShowShowObjectPage(){
		$forestEncounter = factory(\App\ForestEncounter::class)->create();

		$this->callSecure('GET', 'forestEncounters/'.$forestEncounter->id);

		$this->assertResponseOk();

		$this->assertViewHas('forestEncounter');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'forestEncounters/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\ForestEncounter::class)->create();

		$response = $this->callSecure('GET', 'api/forestEncounters');

		$this->assertResponseOk();

		$forestEncounters = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($forestEncounters));

		$forestEncounter = $forestEncounters[0];

		$expectedData = [
			'description' => "description",
			'title' => "title",
			'rolls' => "1d6+2,2d5+10",

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $forestEncounter);
	}
	
	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewForestEncounter(){
		$forestEncounter = [
			'description' => "description",
			'title' => "title",
			'rolls' => "1d6+2,2d5+10",
		];


		$response = $this->call('POST', '/forestEncounters', $forestEncounter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/forestEncounters/1?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(ForestEncounter::all()));

		$storedForestEncounter = ForestEncounter::findById(1);
		$this->assertNotNull($storedForestEncounter);

		$this->assertEquals('title', $storedForestEncounter->title);
		$this->assertEquals('description', $storedForestEncounter->description);
		$this->assertEquals("1d6+2,2d5+10", $storedForestEncounter->rolls);

		$this->assertEquals(0, $storedForestEncounter->approved);
		$this->assertEquals(0, $storedForestEncounter->public);
		$this->assertEquals($this->user->id, $storedForestEncounter->owner_id);
	}

	public function testStoreShouldNotCreateNewForestEncounterWhenForestEncounterInvalid(){
		$forestEncounter = [
			'description' => "description",
			'rolls' => "1d6+2,2d5+10",
		];

		$response = $this->call('POST', '/forestEncounters', $forestEncounter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/forestEncounters/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(ForestEncounter::all()));
	}
	
	public function testUpdateShouldUpdateObject(){
		$forestEncounter = factory(ForestEncounter::class)->create();

		$newForestEncounter = [
			'title' => "this is a new title",
			'id' => $forestEncounter->id
		];

		$storedForestEncounter = ForestEncounter::findById($forestEncounter->id);
		$this->assertEquals("title", $storedForestEncounter->title);

		$response = $this->call('PATCH', 'forestEncounters/'.$forestEncounter->id, $newForestEncounter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/forestEncounters/1?successMessage=Record+Updated+Successfully'));

		$storedForestEncounter = ForestEncounter::findById($forestEncounter->id);
		$this->assertEquals("this is a new title", $storedForestEncounter->title);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		self::ensureTrapOfIdOneExists();

		$forestEncounter = factory(ForestEncounter::class)->create();

		$newForestEncounter = [
			'title' => null,
			'id' => $forestEncounter->id
		];

		$storedForestEncounter = ForestEncounter::findById($forestEncounter->id);
		$this->assertEquals("title", $storedForestEncounter->title);

		$response = $this->call('PATCH', 'forestEncounters/'.$forestEncounter->id, $newForestEncounter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/forestEncounters/'.$forestEncounter->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedForestEncounter = ForestEncounter::findById($forestEncounter->id);
		$this->assertEquals("title", $storedForestEncounter->title);
	}
	
	public function testDestroyShouldDeleteRecord(){
		$forestEncounter = factory(ForestEncounter::class)->create();

		$count = count(ForestEncounter::all());

		$response = $this->call('DELETE', 'forestEncounters/'.$forestEncounter->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/forestEncounters?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(ForestEncounter::all()));
	}

}