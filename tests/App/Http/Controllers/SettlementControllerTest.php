<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\SettlementController;
use App\Settlement;

class SettlementControllerTest extends TestCase
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
		$this->callSecure('GET', 'settlements/create');

		$this->assertResponseOk();

		$this->assertViewHas('settlement');
	}

	public function testEditShouldShowEditObjectPage(){
		$settlement = factory(\App\Settlement::class)->create();

		$this->callSecure('GET', 'settlements/'.$settlement->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('settlement');
	}

	public function testShowShouldShowShowObjectPage(){
		$settlement = factory(\App\Settlement::class)->create();

		$this->callSecure('GET', 'settlements/'.$settlement->id);

		$this->assertResponseOk();

		$this->assertViewHas('settlement');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'settlements/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){

		factory(\App\Settlement::class)->create(['name' => 'foo']);

		$response = $this->callSecure('GET', 'api/settlements');

		$this->assertResponseOk();

		$settlements = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($settlements));

		$settlement = $settlements[0];

		$expectedData = [
			"name" => 'foo',
			"population" => 52,
			"size" => 'S',
			'ruler_id' => 1,
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $settlement);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewSettlement(){
		$settlement = [
			"name" => 'foo',
			"population" => 52,
			"size" => 'S',
			'ruler_id' => 1,
		];


		$response = $this->call('POST', '/settlements', $settlement);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/settlements/1?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(Settlement::all()));

		$storedSettlement = Settlement::findById(1);
		$this->assertNotNull($storedSettlement);

		$this->assertEquals('foo', $storedSettlement->name);
		$this->assertEquals(52, $storedSettlement->population);
		$this->assertEquals('S', $storedSettlement->size);
		$this->assertEquals(1, $storedSettlement->ruler_id);

		$this->assertEquals(0, $storedSettlement->approved);
		$this->assertEquals(0, $storedSettlement->public);
		$this->assertEquals($this->user->id, $storedSettlement->owner_id);
	}

	public function testStoreShouldNotCreateNewSettlementWhenSettlementInvalid(){
		$settlement = [
			"population" => 52,
			"size" => 'S',
			'ruler_id' => 1,
		];

		$response = $this->call('POST', '/settlements', $settlement);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/settlements/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(Settlement::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$settlement = factory(Settlement::class)->create(['name' => 'foo']);

		$newSettlement = [
			"name" => 'foobar',

		];

		$storedSettlement = Settlement::findById($settlement->id);
		$this->assertEquals("foo", $storedSettlement->name);

		$response = $this->call('PATCH', 'settlements/'.$settlement->id, $newSettlement);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/settlements/1?successMessage=Record+Updated+Successfully'));

		$storedSettlement = Settlement::findById($settlement->id);
		$this->assertEquals("foobar", $storedSettlement->name);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$settlement = factory(Settlement::class)->create(['name' => 'foo']);

		$newSettlement = [
			'ruler_id' => -1,
			'id' => $settlement->id
		];

		$storedSettlement = Settlement::findById($settlement->id);
		$this->assertEquals(1, $storedSettlement->ruler_id);

		$response = $this->call('PATCH', 'settlements/'.$settlement->id, $newSettlement);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/settlements/'.$settlement->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedSettlement = Settlement::findById($settlement->id);
		$this->assertEquals(1, $storedSettlement->ruler_id);
	}

	public function testDestroyShouldDeleteRecord(){
		$settlement = factory(Settlement::class)->create();

		$count = count(Settlement::all());

		$response = $this->call('DELETE', 'settlements/'.$settlement->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/settlements?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(Settlement::all()));
	}

}