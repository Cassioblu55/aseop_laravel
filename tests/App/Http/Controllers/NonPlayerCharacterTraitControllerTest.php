<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\NonPlayerCharacterTraitController;

class NonPlayerCharacterTraitControllerTest extends TestCase
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
		$this->callSecure('GET', 'npcTraits/create');

		$this->assertResponseOk();

		$this->assertViewHas('npcTrait');
	}

	public function testEditShouldShowEditObjectPage(){
		$npcTrait = factory(\App\NonPlayerCharacterTrait::class)->create();

		$this->callSecure('GET', 'npcTraits/'.$npcTrait->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('npcTrait');
	}

	public function testShowShouldShowShowObjectPage(){
		$npcTrait = factory(\App\NonPlayerCharacterTrait::class)->create();

		$this->callSecure('GET', 'npcTraits/'.$npcTrait->id);

		$this->assertResponseOk();

		$this->assertViewHas('npcTrait');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'npcTraits/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\NonPlayerCharacterTrait::class)->create();

		$response = $this->callSecure('GET', 'api/npcTraits');

		$this->assertResponseOk();

		$npcTraits = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($npcTraits));

		$npcTrait = $npcTraits[0];

		$expectedData = [
			'type' => 'mannerism',
			'trait' => 'blinks very slowly',
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $npcTrait);
	}

}