<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\RiddleController;
use App\Riddle;

class RiddleControllerTest extends TestCase
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
		$this->callSecure('GET', 'riddles/create');

		$this->assertResponseOk();

		$this->assertViewHas('riddle');
	}

	public function testEditShouldShowEditObjectPage(){
		$riddle = factory(\App\Riddle::class)->create();

		$this->callSecure('GET', 'riddles/'.$riddle->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('riddle');
	}

	public function testShowShouldShowShowObjectPage(){
		$riddle = factory(\App\Riddle::class)->create();

		$this->callSecure('GET', 'riddles/'.$riddle->id);

		$this->assertResponseOk();

		$this->assertViewHas('riddle');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'riddles/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\Riddle::class)->create();

		$response = $this->callSecure('GET', 'api/riddles');

		$this->assertResponseOk();

		$riddles = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($riddles));

		$riddle = $riddles[0];

		$expectedData = [
			'solution' => 'solution',
			'riddle' => 'riddle',
			'name' => 'name',
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $riddle);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewRiddle(){
		$riddle = [
			'solution' => 'solution',
			'riddle' => 'riddle',
			'name' => 'name',
		];


		$response = $this->call('POST', '/riddles', $riddle);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/riddles/1?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(Riddle::all()));

		$storedRiddle = Riddle::findById(1);
		$this->assertNotNull($storedRiddle);

		$this->assertEquals('solution', $storedRiddle->solution);
		$this->assertEquals('riddle', $storedRiddle->riddle);
		$this->assertEquals('name', $storedRiddle->name);

		$this->assertEquals(0, $storedRiddle->approved);
		$this->assertEquals(0, $storedRiddle->public);
		$this->assertEquals($this->user->id, $storedRiddle->owner_id);
	}

	public function testStoreShouldNotCreateNewRiddleWhenRiddleInvalid(){
		$riddle = [
			'solution' => 'solution',
			'riddle' => 'riddle',
		];

		$response = $this->call('POST', '/riddles', $riddle);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/riddles/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(Riddle::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$riddle = factory(Riddle::class)->create();

		$newRiddle = [
			'name' => 'new name',
			'id' => $riddle->id
		];

		$storedRiddle = Riddle::findById($riddle->id);
		$this->assertEquals("name", $storedRiddle->name);

		$response = $this->call('PATCH', 'riddles/'.$riddle->id, $newRiddle);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/riddles/1?successMessage=Record+Updated+Successfully'));

		$storedRiddle = Riddle::findById($riddle->id);
		$this->assertEquals("new name", $storedRiddle->name);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$riddle = factory(Riddle::class)->create();

		$newRiddle = [
			'name' => null,
			'id' => $riddle->id
		];

		$storedRiddle = Riddle::findById($riddle->id);
		$this->assertEquals("name", $storedRiddle->name);

		$response = $this->call('PATCH', 'riddles/'.$riddle->id, $newRiddle);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/riddles/'.$riddle->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedRiddle = Riddle::findById($riddle->id);
		$this->assertEquals("name", $storedRiddle->name);
	}

	public function testDestroyShouldDeleteRecord(){
		$riddle = factory(Riddle::class)->create();

		$count = count(Riddle::all());

		$response = $this->call('DELETE', 'riddles/'.$riddle->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/riddles?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(Riddle::all()));
	}

}