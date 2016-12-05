<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\NonPlayerCharacterTraitController;
use App\NonPlayerCharacterTrait;

class NonPlayerCharacterTraitControllerTest extends TestCase
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
		$this->callSecure('GET', 'npcTraits/create');

		$this->assertResponseOk();

		$this->assertViewHas('npcTrait');
	}

	public function testEditShouldShowEditObjectPage(){
		$npcTrait = factory(\App\NonPlayerCharacterTrait::class)->create();

		$this->callSecure('GET', 'npcTraits/'.$npcTrait->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('npcTrait');
	}

	public function testShowShouldShowShowObjectPage(){
		$npcTrait = factory(\App\NonPlayerCharacterTrait::class)->create();

		$this->callSecure('GET', 'npcTraits/'.$npcTrait->id);

		$this->assertResponseOk();

		$this->assertViewHas('npcTrait');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'npcTraits/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\NonPlayerCharacterTrait::class)->create();

		$response = $this->callSecure('GET', 'api/npcTraits');

		$this->assertResponseOk();

		$npcTraits = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($npcTraits));

		$npcTrait = $npcTraits[0];

		$expectedData = [
			'type' => 'mannerism',
			'trait' => 'blinks very slowly',
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $npcTrait);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewNonPlayerCharacterTrait(){
		$nonPlayerCharacterTrait = [
			'type' => 'mannerism',
			'trait' => 'blinks very slowly',
		];

		$response = $this->call('POST', '/npcTraits', $nonPlayerCharacterTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/npcTraits?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(NonPlayerCharacterTrait::all()));

		$storedNonPlayerCharacterTrait = NonPlayerCharacterTrait::findById(1);
		$this->assertNotNull($storedNonPlayerCharacterTrait);

		$this->assertEquals('mannerism', $storedNonPlayerCharacterTrait->type);
		$this->assertEquals('blinks very slowly', $storedNonPlayerCharacterTrait->trait);

		$this->assertEquals(0, $storedNonPlayerCharacterTrait->approved);
		$this->assertEquals(0, $storedNonPlayerCharacterTrait->public);
		$this->assertEquals($this->user->id, $storedNonPlayerCharacterTrait->owner_id);
	}

	public function testStoreShouldNotCreateNewNonPlayerCharacterTraitWhenNonPlayerCharacterTraitInvalid(){
		$nonPlayerCharacterTrait = [
			'trait' => 'blinks very slowly',
		];

		$response = $this->call('POST', '/npcTraits', $nonPlayerCharacterTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/npcTraits/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(NonPlayerCharacterTrait::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$nonPlayerCharacterTrait = factory(NonPlayerCharacterTrait::class)->create();

		$newNonPlayerCharacterTrait = [
			'trait' => 'blinks super slowly',
			'id' => $nonPlayerCharacterTrait->id
		];

		$storedNonPlayerCharacterTrait = NonPlayerCharacterTrait::findById($nonPlayerCharacterTrait->id);
		$this->assertEquals("blinks very slowly", $storedNonPlayerCharacterTrait->trait);

		$response = $this->call('PATCH', 'npcTraits/'.$nonPlayerCharacterTrait->id, $newNonPlayerCharacterTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/npcTraits?successMessage=Record+Updated+Successfully'));

		$storedNonPlayerCharacterTrait = NonPlayerCharacterTrait::findById($nonPlayerCharacterTrait->id);
		$this->assertEquals("blinks super slowly", $storedNonPlayerCharacterTrait->trait);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$nonPlayerCharacterTrait = factory(NonPlayerCharacterTrait::class)->create();

		$newNonPlayerCharacterTrait = [
			'trait' => null,
			'id' => $nonPlayerCharacterTrait->id
		];

		$storedNonPlayerCharacterTrait = NonPlayerCharacterTrait::findById($nonPlayerCharacterTrait->id);
		$this->assertEquals("blinks very slowly", $storedNonPlayerCharacterTrait->trait);

		$response = $this->call('PATCH', 'npcTraits/'.$nonPlayerCharacterTrait->id, $newNonPlayerCharacterTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/npcTraits/'.$nonPlayerCharacterTrait->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedNonPlayerCharacterTrait = NonPlayerCharacterTrait::findById($nonPlayerCharacterTrait->id);
		$this->assertEquals("blinks very slowly", $storedNonPlayerCharacterTrait->trait);
	}

	public function testDestroyShouldDeleteRecord(){
		$nonPlayerCharacterTrait = factory(NonPlayerCharacterTrait::class)->create();

		$count = count(NonPlayerCharacterTrait::all());

		$response = $this->call('DELETE', 'npcTraits/'.$nonPlayerCharacterTrait->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/npcTraits?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(NonPlayerCharacterTrait::all()));
	}

}