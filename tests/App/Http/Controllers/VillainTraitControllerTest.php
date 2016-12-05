<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\VillainTraitController;
use App\VillainTrait;

class VillainTraitControllerTest extends TestCase
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
		$this->callSecure('GET', 'villainTraits/create');

		$this->assertResponseOk();

		$this->assertViewHas('villainTrait');
	}

	public function testEditShouldShowEditObjectPage(){
		$villainTrait = factory(\App\VillainTrait::class)->create();

		$this->callSecure('GET', 'villainTraits/'.$villainTrait->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('villainTrait');
	}

	public function testShowShouldShowShowObjectPage(){
		$villainTrait = factory(\App\VillainTrait::class)->create();

		$this->callSecure('GET', 'villainTraits/'.$villainTrait->id);

		$this->assertResponseOk();

		$this->assertViewHas('villainTrait');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'villainTraits/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\VillainTrait::class)->create();

		$response = $this->callSecure('GET', 'api/villainTraits');

		$this->assertResponseOk();

		$villainTraits = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($villainTraits));

		$villainTrait = $villainTraits[0];

		$expectedData = [
			'type' => "scheme",
			'kind' => "Immortality",
			'description' => "description",

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $villainTrait);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewVillainTrait(){
		$villainTrait = [
			'type' => "scheme",
			'kind' => "Immortality",
			'description' => "description"
		];


		$response = $this->call('POST', '/villainTraits', $villainTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/villainTraits?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(VillainTrait::all()));

		$storedVillainTrait = VillainTrait::findById(1);
		$this->assertNotNull($storedVillainTrait);

		$this->assertEquals('scheme', $storedVillainTrait->type);
		$this->assertEquals('Immortality', $storedVillainTrait->kind);
		$this->assertEquals('description', $storedVillainTrait->description);

		$this->assertEquals(0, $storedVillainTrait->approved);
		$this->assertEquals(0, $storedVillainTrait->public);
		$this->assertEquals($this->user->id, $storedVillainTrait->owner_id);
	}

	public function testStoreShouldNotCreateNewVillainTraitWhenVillainTraitInvalid(){
		$villainTrait = [
			'type' => "scheme",
			'description' => "description"
		];

		$response = $this->call('POST', '/villainTraits', $villainTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/villainTraits/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(VillainTrait::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$villainTrait = factory(VillainTrait::class)->create();

		$newVillainTrait = [
			'description' => "new description",
			'id' => $villainTrait->id
		];

		$storedVillainTrait = VillainTrait::findById($villainTrait->id);
		$this->assertEquals("description", $storedVillainTrait->description);

		$response = $this->call('PATCH', 'villainTraits/'.$villainTrait->id, $newVillainTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/villainTraits?successMessage=Record+Updated+Successfully'));

		$storedVillainTrait = VillainTrait::findById($villainTrait->id);
		$this->assertEquals("new description", $storedVillainTrait->description);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$villainTrait = factory(VillainTrait::class)->create();

		$newVillainTrait = [
			'type' => null,
			'id' => $villainTrait->id
		];

		$storedVillainTrait = VillainTrait::findById($villainTrait->id);
		$this->assertEquals("scheme", $storedVillainTrait->type);

		$response = $this->call('PATCH', 'villainTraits/'.$villainTrait->id, $newVillainTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/villainTraits/'.$villainTrait->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedVillainTrait = VillainTrait::findById($villainTrait->id);
		$this->assertEquals("scheme", $storedVillainTrait->type);
	}

	public function testDestroyShouldDeleteRecord(){
		$villainTrait = factory(VillainTrait::class)->create();

		$count = count(VillainTrait::all());

		$response = $this->call('DELETE', 'villainTraits/'.$villainTrait->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/villainTraits?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(VillainTrait::all()));
	}

}