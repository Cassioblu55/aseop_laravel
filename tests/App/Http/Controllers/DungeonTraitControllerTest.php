<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\DungeonTraitController;

class DungeonTraitControllerTest extends TestCase
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
		$this->callSecure('GET', 'dungeonTraits/create');

		$this->assertResponseOk();

		$this->assertViewHas('dungeonTrait');
	}

	public function testEditShouldShowEditObjectPage(){
		$dungeonTrait = factory(\App\DungeonTrait::class)->create();

		$this->callSecure('GET', 'dungeonTraits/'.$dungeonTrait->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('dungeonTrait');
	}

	public function testShowShouldShowShowObjectPage(){
		$dungeonTrait = factory(\App\DungeonTrait::class)->create();

		$this->callSecure('GET', 'dungeonTraits/'.$dungeonTrait->id);

		$this->assertResponseOk();

		$this->assertViewHas('dungeonTrait');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'dungeonTraits/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\DungeonTrait::class)->create();

		$response = $this->callSecure('GET', 'api/dungeonTraits');

		$this->assertResponseOk();

		$dungeonTraits = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($dungeonTraits));

		$dungeonTrait = $dungeonTraits[0];

		$expectedData = [
			'type' => "name",
			'trait' => "bar",

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $dungeonTrait);
	}

}