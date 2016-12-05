<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\TavernTraitController;
use App\TavernTrait;

class TavernTraitControllerTest extends TestCase
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
		$this->callSecure('GET', 'tavernTraits/create');

		$this->assertResponseOk();

		$this->assertViewHas('tavernTrait');
	}

	public function testEditShouldShowEditObjectPage(){
		$tavernTrait = factory(\App\TavernTrait::class)->create();

		$this->callSecure('GET', 'tavernTraits/'.$tavernTrait->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('tavernTrait');
	}

	public function testShowShouldShowShowObjectPage(){
		$tavernTrait = factory(\App\TavernTrait::class)->create();

		$this->callSecure('GET', 'tavernTraits/'.$tavernTrait->id);

		$this->assertResponseOk();

		$this->assertViewHas('tavernTrait');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'tavernTraits/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\TavernTrait::class)->create();

		$response = $this->callSecure('GET', 'api/tavernTraits');

		$this->assertResponseOk();

		$tavernTraits = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($tavernTraits));

		$tavernTrait = $tavernTraits[0];

		$expectedData = [
			'trait' => 'Dive Bar',
			'type' => 'type',

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $tavernTrait);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewTavernTrait(){
		$tavernTrait = [
			'trait' => 'Dive Bar',
			'type' => 'type',
		];


		$response = $this->call('POST', '/tavernTraits', $tavernTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/tavernTraits?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(TavernTrait::all()));

		$storedTavernTrait = TavernTrait::findById(1);
		$this->assertNotNull($storedTavernTrait);

		$this->assertEquals('Dive Bar', $storedTavernTrait->trait);
		$this->assertEquals('type', $storedTavernTrait->type);

		$this->assertEquals(0, $storedTavernTrait->approved);
		$this->assertEquals(0, $storedTavernTrait->public);
		$this->assertEquals($this->user->id, $storedTavernTrait->owner_id);
	}

	public function testStoreShouldNotCreateNewTavernTraitWhenTavernTraitInvalid(){
		$tavernTrait = [
			'type' => 'type',
		];

		$response = $this->call('POST', '/tavernTraits', $tavernTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/tavernTraits/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(TavernTrait::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$tavernTrait = factory(TavernTrait::class)->create();

		$newTavernTrait = [
			'trait' => 'Divine Bar',
			'id' => $tavernTrait->id
		];

		$storedTavernTrait = TavernTrait::findById($tavernTrait->id);
		$this->assertEquals("Dive Bar", $storedTavernTrait->trait);

		$response = $this->call('PATCH', 'tavernTraits/'.$tavernTrait->id, $newTavernTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/tavernTraits?successMessage=Record+Updated+Successfully'));

		$storedTavernTrait = TavernTrait::findById($tavernTrait->id);
		$this->assertEquals("Divine Bar", $storedTavernTrait->trait);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$tavernTrait = factory(TavernTrait::class)->create();

		$newTavernTrait = [
			'trait' => null,
			'id' => $tavernTrait->id
		];

		$storedTavernTrait = TavernTrait::findById($tavernTrait->id);
		$this->assertEquals("Dive Bar", $storedTavernTrait->trait);

		$response = $this->call('PATCH', 'tavernTraits/'.$tavernTrait->id, $newTavernTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/tavernTraits/'.$tavernTrait->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedTavernTrait = TavernTrait::findById($tavernTrait->id);
		$this->assertEquals("Dive Bar", $storedTavernTrait->trait);
	}

	public function testDestroyShouldDeleteRecord(){
		$tavernTrait = factory(TavernTrait::class)->create();

		$count = count(TavernTrait::all());

		$response = $this->call('DELETE', 'tavernTraits/'.$tavernTrait->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/tavernTraits?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(TavernTrait::all()));
	}

}