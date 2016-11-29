<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\TavernTraitController;

class TavernTraitControllerTest extends TestCase
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
		$this->callSecure('GET', 'tavernTraits/create');

		$this->assertResponseOk();

		$this->assertViewHas('tavernTrait');
	}

	public function testEditShouldShowEditObjectPage(){
		$tavernTrait = factory(\App\TavernTrait::class)->create();

		$this->callSecure('GET', 'tavernTraits/'.$tavernTrait->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('tavernTrait');
	}

	public function testShowShouldShowShowObjectPage(){
		$tavernTrait = factory(\App\TavernTrait::class)->create();

		$this->callSecure('GET', 'tavernTraits/'.$tavernTrait->id);

		$this->assertResponseOk();

		$this->assertViewHas('tavernTrait');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'tavernTraits/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\TavernTrait::class)->create();

		$response = $this->callSecure('GET', 'api/tavernTraits');

		$this->assertResponseOk();

		$tavernTraits = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($tavernTraits));

		$tavernTrait = $tavernTraits[0];

		$expectedData = [
			'trait' => 'Dive Bar',
			'type' => 'type',

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $tavernTrait);
	}

}