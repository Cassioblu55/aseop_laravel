<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\UrbanEncounterController;
use App\UrbanEncounter;

class UrbanEncounterControllerTest extends TestCase
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
		$this->callSecure('GET', 'urbanEncounters/create');

		$this->assertResponseOk();

		$this->assertViewHas('urbanEncounter');
	}

	public function testEditShouldShowEditObjectPage(){
		$urbanEncounter = factory(\App\UrbanEncounter::class)->create();

		$this->callSecure('GET', 'urbanEncounters/'.$urbanEncounter->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('urbanEncounter');
	}

	public function testShowShouldShowShowObjectPage(){
		$urbanEncounter = factory(\App\UrbanEncounter::class)->create();

		$this->callSecure('GET', 'urbanEncounters/'.$urbanEncounter->id);

		$this->assertResponseOk();

		$this->assertViewHas('urbanEncounter');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'urbanEncounters/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\UrbanEncounter::class)->create();

		$response = $this->callSecure('GET', 'api/urbanEncounters');

		$this->assertResponseOk();

		$urbanEncounters = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($urbanEncounters));

		$urbanEncounter = $urbanEncounters[0];

		$expectedData = [
			'description' => "description",
			'title' => "title",
			'rolls' => "1d6+2,2d5+10",

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $urbanEncounter);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewUrbanEncounter(){
		$urbanEncounter = [
			'description' => "description",
			'title' => "title",
			'rolls' => "1d6+2,2d5+10",
		];


		$response = $this->call('POST', '/urbanEncounters', $urbanEncounter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/urbanEncounters/1?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(UrbanEncounter::all()));

		$storedUrbanEncounter = UrbanEncounter::findById(1);
		$this->assertNotNull($storedUrbanEncounter);

		$this->assertEquals('title', $storedUrbanEncounter->title);
		$this->assertEquals('description', $storedUrbanEncounter->description);
		$this->assertEquals('1d6+2,2d5+10', $storedUrbanEncounter->rolls);

		$this->assertEquals(0, $storedUrbanEncounter->approved);
		$this->assertEquals(0, $storedUrbanEncounter->public);
		$this->assertEquals($this->user->id, $storedUrbanEncounter->owner_id);
	}

	public function testStoreShouldNotCreateNewUrbanEncounterWhenUrbanEncounterInvalid(){
		$urbanEncounter = [
			'description' => "description",
			'rolls' => "1d6+2,2d5+10",
		];

		$response = $this->call('POST', '/urbanEncounters', $urbanEncounter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/urbanEncounters/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(UrbanEncounter::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$urbanEncounter = factory(UrbanEncounter::class)->create();

		$newUrbanEncounter = [
			'title' => "new title",
			'id' => $urbanEncounter->id
		];

		$storedUrbanEncounter = UrbanEncounter::findById($urbanEncounter->id);
		$this->assertEquals("title", $storedUrbanEncounter->title);

		$response = $this->call('PATCH', 'urbanEncounters/'.$urbanEncounter->id, $newUrbanEncounter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/urbanEncounters/1?successMessage=Record+Updated+Successfully'));

		$storedUrbanEncounter = UrbanEncounter::findById($urbanEncounter->id);
		$this->assertEquals("new title", $storedUrbanEncounter->title);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$urbanEncounter = factory(UrbanEncounter::class)->create();

		$newUrbanEncounter = [
			'title' => null,
			'id' => $urbanEncounter->id
		];

		$storedUrbanEncounter = UrbanEncounter::findById($urbanEncounter->id);
		$this->assertEquals("title", $storedUrbanEncounter->title);

		$response = $this->call('PATCH', 'urbanEncounters/'.$urbanEncounter->id, $newUrbanEncounter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/urbanEncounters/'.$urbanEncounter->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedUrbanEncounter = UrbanEncounter::findById($urbanEncounter->id);
		$this->assertEquals("title", $storedUrbanEncounter->title);
	}

	public function testDestroyShouldDeleteRecord(){
		$urbanEncounter = factory(UrbanEncounter::class)->create();

		$count = count(UrbanEncounter::all());

		$response = $this->call('DELETE', 'urbanEncounters/'.$urbanEncounter->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/urbanEncounters?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(UrbanEncounter::all()));
	}

}