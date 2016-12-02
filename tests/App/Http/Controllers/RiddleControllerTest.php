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

}