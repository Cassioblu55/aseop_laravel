<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\TavernController;
use App\Tavern;

class TavernControllerTest extends TestCase
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
		$this->callSecure('GET', 'taverns/create');

		$this->assertResponseOk();

		$this->assertViewHas('tavern');
	}

	public function testEditShouldShowEditObjectPage(){
		$tavern = factory(\App\Tavern::class)->create();

		$this->callSecure('GET', 'taverns/'.$tavern->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('tavern');
	}

	public function testShowShouldShowShowObjectPage(){
		$tavern = factory(\App\Tavern::class)->create();

		$this->callSecure('GET', 'taverns/'.$tavern->id);

		$this->assertResponseOk();

		$this->assertViewHas('tavern');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'taverns/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\Tavern::class)->create();

		$response = $this->callSecure('GET', 'api/taverns');

		$this->assertResponseOk();

		$taverns = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($taverns));

		$tavern = $taverns[0];

		$expectedData = [
			'name' => 'The Golden Rat',
			'type' => 'Thieves Guild Hangout',
			'tavern_owner_id' => 1,
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $tavern);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewTavern(){
		$tavern = [
			'name' => 'The Golden Rat',
			'type' => 'Thieves Guild Hangout',
			'tavern_owner_id' => 1,
		];


		$response = $this->call('POST', '/taverns', $tavern);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/taverns/1?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(Tavern::all()));

		$storedTavern = Tavern::findById(1);
		$this->assertNotNull($storedTavern);

		$this->assertEquals('The Golden Rat', $storedTavern->name);
		$this->assertEquals('Thieves Guild Hangout', $storedTavern->type);
		$this->assertEquals(1, $storedTavern->tavern_owner_id);

		$this->assertEquals(0, $storedTavern->approved);
		$this->assertEquals(0, $storedTavern->public);
		$this->assertEquals($this->user->id, $storedTavern->owner_id);
	}

	public function testStoreShouldNotCreateNewTavernWhenTavernInvalid(){
		$tavern = [
			'type' => 'Thieves Guild Hangout',
			'tavern_owner_id' => 1,
		];

		$response = $this->call('POST', '/taverns', $tavern);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/taverns/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(Tavern::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$tavern = factory(Tavern::class)->create();

		$newTavern = [
			'name' => 'The Golden Ratty',
			'id' => $tavern->id
		];

		$storedTavern = Tavern::findById($tavern->id);
		$this->assertEquals("The Golden Rat", $storedTavern->name);

		$response = $this->call('PATCH', 'taverns/'.$tavern->id, $newTavern);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/taverns/1?successMessage=Record+Updated+Successfully'));

		$storedTavern = Tavern::findById($tavern->id);
		$this->assertEquals("The Golden Ratty", $storedTavern->name);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$tavern = factory(Tavern::class)->create();

		$newTavern = [
			'name' => null,
			'id' => $tavern->id
		];

		$storedTavern = Tavern::findById($tavern->id);
		$this->assertEquals("The Golden Rat", $storedTavern->name);

		$response = $this->call('PATCH', 'taverns/'.$tavern->id, $newTavern);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/taverns/'.$tavern->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedTavern = Tavern::findById($tavern->id);
		$this->assertEquals("The Golden Rat", $storedTavern->name);
	}

	public function testDestroyShouldDeleteRecord(){
		$tavern = factory(Tavern::class)->create();

		$count = count(Tavern::all());

		$response = $this->call('DELETE', 'taverns/'.$tavern->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/taverns?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(Tavern::all()));
	}

}