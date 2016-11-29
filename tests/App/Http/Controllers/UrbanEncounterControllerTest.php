<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\UrbanEncounterController;

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

}