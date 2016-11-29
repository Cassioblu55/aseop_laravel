<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\NonPlayerCharacterController;

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

}