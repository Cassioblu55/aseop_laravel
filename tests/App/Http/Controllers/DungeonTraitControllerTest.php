<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\DungeonTraitController;
use App\DungeonTrait;

class DungeonTraitControllerTest extends TestCase
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
		$this->callSecure('GET', 'dungeonTraits/create');

		$this->assertResponseOk();

		$this->assertViewHas('dungeonTrait');
	}

	public function testStoreShouldCreateNewDungeonTrait(){
		$dungeonTrait = [
			'type' => "name",
			'trait' => "foo"
		];

		$response = $this->call('POST', '/dungeonTraits', $dungeonTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/dungeonTraits?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(DungeonTrait::all()));

		$storedDungeonTrait = DungeonTrait::findById(1);
		$this->assertNotNull($storedDungeonTrait);

		$this->assertEquals('name', $storedDungeonTrait->type);
		$this->assertEquals('foo', $storedDungeonTrait->trait);

		$this->assertEquals(0, $storedDungeonTrait->approved);
		$this->assertEquals(0, $storedDungeonTrait->public);
		$this->assertEquals($this->user->id, $storedDungeonTrait->owner_id);
	}

	public function testStoreShouldNotCreateNewDungeonTraitWhenDungeonTraitInvalid(){
		$dungeonTrait = [
			'type' => "not a valid type",
			'trait' => "foo"
		];

		$response = $this->call('POST', '/dungeonTraits', $dungeonTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/dungeonTraits/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(DungeonTrait::all()));
	}

	public function testEditShouldShowEditObjectPage(){
		$dungeonTrait = factory(\App\DungeonTrait::class)->create();

		$this->callSecure('GET', 'dungeonTraits/'.$dungeonTrait->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('dungeonTrait');
	}

	public function testUpdateShouldUpdateObject(){
		$dungeonTrait = factory(DungeonTrait::class)->create();

		$newDungeonTrait = [
			'trait' => "This is the new Name",
			'id' => $dungeonTrait->id
		];

		$storedDungeonTrait = DungeonTrait::findById($dungeonTrait->id);
		$this->assertEquals("bar", $storedDungeonTrait->trait);

		$response = $this->call('PATCH', 'dungeonTraits/'.$dungeonTrait->id, $newDungeonTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/dungeonTraits?successMessage=Record+Updated+Successfully'));

		$storedDungeonTrait = DungeonTrait::findById($dungeonTrait->id);
		$this->assertEquals("This is the new Name", $storedDungeonTrait->trait);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		self::ensureTrapOfIdOneExists();

		$dungeonTrait = factory(DungeonTrait::class)->create();

		$newDungeonTrait = [
			'trait' => null,
			'id' => $dungeonTrait->id
		];

		$storedDungeonTrait = DungeonTrait::findById($dungeonTrait->id);
		$this->assertEquals("bar", $storedDungeonTrait->trait);

		$response = $this->call('PATCH', 'dungeonTraits/'.$dungeonTrait->id, $newDungeonTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/dungeonTraits/'.$dungeonTrait->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedDungeonTrait = DungeonTrait::findById($dungeonTrait->id);
		$this->assertEquals("bar", $storedDungeonTrait->trait);
	}

	public function testShowShouldShowShowObjectPage(){
		$dungeonTrait = factory(\App\DungeonTrait::class)->create();

		$this->callSecure('GET', 'dungeonTraits/'.$dungeonTrait->id);

		$this->assertResponseOk();

		$this->assertViewHas('dungeonTrait');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'dungeonTraits/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\DungeonTrait::class)->create();

		$response = $this->callSecure('GET', 'api/dungeonTraits');

		$this->assertResponseOk();

		$dungeonTraits = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($dungeonTraits));

		$dungeonTrait = $dungeonTraits[0];

		$expectedData = [
			'type' => "name",
			'trait' => "bar",

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $dungeonTrait);
	}

	public function testDestroyShouldDeleteRecord(){
		$dungeonTrait = factory(DungeonTrait::class)->create();

		$count = count(DungeonTrait::all());

		$response = $this->call('DELETE', 'dungeonTraits/'.$dungeonTrait->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/dungeonTraits?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(DungeonTrait::all()));
	}
}