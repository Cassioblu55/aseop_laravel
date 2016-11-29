<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\VillainController;

class VillainControllerTest extends TestCase
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
		$this->callSecure('GET', 'villains/create');

		$this->assertResponseOk();

		$this->assertViewHas('villain');
	}

	public function testEditShouldShowEditObjectPage(){
		$villain = factory(\App\Villain::class)->create();

		$this->callSecure('GET', 'villains/'.$villain->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('villain');
	}

	public function testShowShouldShowShowObjectPage(){
		$villain = factory(\App\Villain::class)->create();

		$this->callSecure('GET', 'villains/'.$villain->id);

		$this->assertResponseOk();

		$this->assertViewHas('villain');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'villains/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){

		factory(\App\Villain::class)->create();

		$response = $this->callSecure('GET', 'api/villains');

		$this->assertResponseOk();

		$villains = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($villains));

		$villain = $villains[0];

		$expectedData = [
			'npc_id' => 1,
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $villain);
	}

}