<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\SettlementTraitController;
use App\SettlementTrait;

class SettlementTraitControllerTest extends TestCase
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
		$this->callSecure('GET', 'settlementTraits/create');

		$this->assertResponseOk();

		$this->assertViewHas('settlementTrait');
	}

	public function testEditShouldShowEditObjectPage(){
		$settlementTrait = factory(\App\SettlementTrait::class)->create();

		$this->callSecure('GET', 'settlementTraits/'.$settlementTrait->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('settlementTrait');
	}

	public function testShowShouldShowShowObjectPage(){
		$settlementTrait = factory(\App\SettlementTrait::class)->create();

		$this->callSecure('GET', 'settlementTraits/'.$settlementTrait->id);

		$this->assertResponseOk();

		$this->assertViewHas('settlementTrait');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'settlementTraits/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\SettlementTrait::class)->create();

		$response = $this->callSecure('GET', 'api/settlementTraits');

		$this->assertResponseOk();

		$settlementTraits = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($settlementTraits));

		$settlementTrait = $settlementTraits[0];

		$expectedData = [
			'type' => 'name',
			'trait' => 'Coolsville',
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $settlementTrait);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewSettlementTrait(){
		$settlementTrait = [
			'type' => 'name',
			'trait' => 'Coolsville',
		];


		$response = $this->call('POST', '/settlementTraits', $settlementTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/settlementTraits?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(SettlementTrait::all()));

		$storedSettlementTrait = SettlementTrait::findById(1);
		$this->assertNotNull($storedSettlementTrait);

		$this->assertEquals('name', $storedSettlementTrait->type);
		$this->assertEquals('Coolsville', $storedSettlementTrait->trait);

		$this->assertEquals(0, $storedSettlementTrait->approved);
		$this->assertEquals(0, $storedSettlementTrait->public);
		$this->assertEquals($this->user->id, $storedSettlementTrait->owner_id);
	}

	public function testStoreShouldNotCreateNewSettlementTraitWhenSettlementTraitInvalid(){
		$settlementTrait = [
			'trait' => 'Coolsville',
		];

		$response = $this->call('POST', '/settlementTraits', $settlementTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/settlementTraits/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(SettlementTrait::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$settlementTrait = factory(SettlementTrait::class)->create();

		$newSettlementTrait = [
			'trait' => 'Coolland',
			'id' => $settlementTrait->id
		];

		$storedSettlementTrait = SettlementTrait::findById($settlementTrait->id);
		$this->assertEquals("Coolsville", $storedSettlementTrait->trait);

		$response = $this->call('PATCH', 'settlementTraits/'.$settlementTrait->id, $newSettlementTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/settlementTraits?successMessage=Record+Updated+Successfully'));

		$storedSettlementTrait = SettlementTrait::findById($settlementTrait->id);
		$this->assertEquals("Coolland", $storedSettlementTrait->trait);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$settlementTrait = factory(SettlementTrait::class)->create();

		$newSettlementTrait = [
			'trait' => null,
			'id' => $settlementTrait->id
		];

		$storedSettlementTrait = SettlementTrait::findById($settlementTrait->id);
		$this->assertEquals("Coolsville", $storedSettlementTrait->trait);

		$response = $this->call('PATCH', 'settlementTraits/'.$settlementTrait->id, $newSettlementTrait);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/settlementTraits/'.$settlementTrait->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedSettlementTrait = SettlementTrait::findById($settlementTrait->id);
		$this->assertEquals("Coolsville", $storedSettlementTrait->trait);
	}

	public function testDestroyShouldDeleteRecord(){
		$settlementTrait = factory(SettlementTrait::class)->create();

		$count = count(SettlementTrait::all());

		$response = $this->call('DELETE', 'settlementTraits/'.$settlementTrait->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/settlementTraits?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(SettlementTrait::all()));
	}

}