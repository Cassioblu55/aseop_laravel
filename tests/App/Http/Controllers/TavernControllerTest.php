<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\TavernController;
use App\Tavern;

class TavernControllerTest extends TestCase
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
		$this->callSecure('GET', 'taverns/create');

		$this->assertResponseOk();

		$this->assertViewHas('tavern');
	}

	public function testEditShouldShowEditObjectPage(){
		$tavern = factory(\App\Tavern::class)->create();

		$this->callSecure('GET', 'taverns/'.$tavern->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('tavern');
	}

	public function testShowShouldShowShowObjectPage(){
		$tavern = factory(\App\Tavern::class)->create();

		$this->callSecure('GET', 'taverns/'.$tavern->id);

		$this->assertResponseOk();

		$this->assertViewHas('tavern');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'taverns/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\Tavern::class)->create();

		$response = $this->callSecure('GET', 'api/taverns');

		$this->assertResponseOk();

		$taverns = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($taverns));

		$tavern = $taverns[0];

		$expectedData = [
			'name' => 'The Golden Rat',
			'type' => 'Thieves Guild Hangout',
			'tavern_owner_id' => 1,
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $tavern);
	}

}