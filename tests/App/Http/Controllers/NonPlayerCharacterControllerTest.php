<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\NonPlayerCharacterController;
use App\NonPlayerCharacter;

class NonPlayerCharacterControllerTest extends TestCase
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
		$this->callSecure('GET', 'npcs/create');

		$this->assertResponseOk();

		$this->assertViewHas('npc');
	}

	public function testEditShouldShowEditObjectPage(){
		$npc = factory(\App\NonPlayerCharacter::class)->create();

		$this->callSecure('GET', 'npcs/'.$npc->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('npc');
	}

	public function testShowShouldShowShowObjectPage(){
		$npc = factory(\App\NonPlayerCharacter::class)->create();

		$this->callSecure('GET', 'npcs/'.$npc->id);

		$this->assertResponseOk();

		$this->assertViewHas('npc');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'npcs/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\NonPlayerCharacter::class)->create();

		$response = $this->callSecure('GET', 'api/npcs');

		$this->assertResponseOk();

		$npcs = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($npcs));

		$npc = $npcs[0];

		$expectedData = [
			'first_name' => 'Bill',
			'sex' => 'M',
			'height' => 65,
			'weight' => 185,
			'age' => 35,

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $npc);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewNonPlayerCharacter(){
		$nonPlayerCharacter = [
			'first_name' => 'Bill',
			'sex' => 'M',
			'height' => 65,
			'weight' => 185,
			'age' => 35,
		];


		$response = $this->call('POST', '/npcs', $nonPlayerCharacter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/npcs/1?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(NonPlayerCharacter::all()));

		$storedNonPlayerCharacter = NonPlayerCharacter::findById(1);
		$this->assertNotNull($storedNonPlayerCharacter);

		$this->assertEquals('Bill', $storedNonPlayerCharacter->first_name);
		$this->assertEquals('M', $storedNonPlayerCharacter->sex);
		$this->assertEquals(65, $storedNonPlayerCharacter->height);
		$this->assertEquals(185, $storedNonPlayerCharacter->weight);
		$this->assertEquals(35, $storedNonPlayerCharacter->age);

		$this->assertEquals(0, $storedNonPlayerCharacter->approved);
		$this->assertEquals(0, $storedNonPlayerCharacter->public);
		$this->assertEquals($this->user->id, $storedNonPlayerCharacter->owner_id);
	}

	public function testStoreShouldNotCreateNewNonPlayerCharacterWhenNonPlayerCharacterInvalid(){
		$nonPlayerCharacter = [
			'sex' => 'M',
			'height' => 65,
			'weight' => 185,
			'age' => 35,
		];

		$response = $this->call('POST', '/npcs', $nonPlayerCharacter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/npcs/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(NonPlayerCharacter::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$nonPlayerCharacter = factory(NonPlayerCharacter::class)->create();

		$newNonPlayerCharacter = [
			'first_name' => 'Billy',
			'id' => $nonPlayerCharacter->id
		];

		$storedNonPlayerCharacter = NonPlayerCharacter::findById($nonPlayerCharacter->id);
		$this->assertEquals("Bill", $storedNonPlayerCharacter->first_name);

		$response = $this->call('PATCH', 'npcs/'.$nonPlayerCharacter->id, $newNonPlayerCharacter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/npcs/1?successMessage=Record+Updated+Successfully'));

		$storedNonPlayerCharacter = NonPlayerCharacter::findById($nonPlayerCharacter->id);
		$this->assertEquals("Billy", $storedNonPlayerCharacter->first_name);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		self::ensureTrapOfIdOneExists();

		$nonPlayerCharacter = factory(NonPlayerCharacter::class)->create();

		$newNonPlayerCharacter = [
			'first_name' => null,
			'id' => $nonPlayerCharacter->id
		];

		$storedNonPlayerCharacter = NonPlayerCharacter::findById($nonPlayerCharacter->id);
		$this->assertEquals("Bill", $storedNonPlayerCharacter->first_name);

		$response = $this->call('PATCH', 'npcs/'.$nonPlayerCharacter->id, $newNonPlayerCharacter);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/npcs/'.$nonPlayerCharacter->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedNonPlayerCharacter = NonPlayerCharacter::findById($nonPlayerCharacter->id);
		$this->assertEquals("Bill", $storedNonPlayerCharacter->first_name);
	}

	public function testDestroyShouldDeleteRecord(){
		$nonPlayerCharacter = factory(NonPlayerCharacter::class)->create();

		$count = count(NonPlayerCharacter::all());

		$response = $this->call('DELETE', 'npcs/'.$nonPlayerCharacter->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/npcs?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(NonPlayerCharacter::all()));
	}

}