<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\ForestEncounterController;

class ForestEncounterControllerTest extends TestCase
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
		$this->callSecure('GET', 'forestEncounters/create');

		$this->assertResponseOk();

		$this->assertViewHas('forestEncounter');
	}

	public function testEditShouldShowEditObjectPage(){
		$forestEncounter = factory(\App\ForestEncounter::class)->create();

		$this->callSecure('GET', 'forestEncounters/'.$forestEncounter->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('forestEncounter');
	}

	public function testShowShouldShowShowObjectPage(){
		$forestEncounter = factory(\App\ForestEncounter::class)->create();

		$this->callSecure('GET', 'forestEncounters/'.$forestEncounter->id);

		$this->assertResponseOk();

		$this->assertViewHas('forestEncounter');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'forestEncounters/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\ForestEncounter::class)->create();

		$response = $this->callSecure('GET', 'api/forestEncounters');

		$this->assertResponseOk();

		$forestEncounters = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($forestEncounters));

		$forestEncounter = $forestEncounters[0];

		$expectedData = [
			'description' => "description",
			'title' => "title",
			'rolls' => "1d6+2,2d5+10",

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $forestEncounter);
	}

}