<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\VillainTraitController;
use App\VillainTrait;

class VillainTraitControllerTest extends TestCase
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
		$this->callSecure('GET', 'villainTraits/create');

		$this->assertResponseOk();

		$this->assertViewHas('villainTrait');
	}

	public function testEditShouldShowEditObjectPage(){
		$villainTrait = factory(\App\VillainTrait::class)->create();

		$this->callSecure('GET', 'villainTraits/'.$villainTrait->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('villainTrait');
	}

	public function testShowShouldShowShowObjectPage(){
		$villainTrait = factory(\App\VillainTrait::class)->create();

		$this->callSecure('GET', 'villainTraits/'.$villainTrait->id);

		$this->assertResponseOk();

		$this->assertViewHas('villainTrait');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'villainTraits/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\VillainTrait::class)->create();

		$response = $this->callSecure('GET', 'api/villainTraits');

		$this->assertResponseOk();

		$villainTraits = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($villainTraits));

		$villainTrait = $villainTraits[0];

		$expectedData = [
			'type' => "scheme",
			'kind' => "Immortality",
			'description' => "description",

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $villainTrait);
	}

}