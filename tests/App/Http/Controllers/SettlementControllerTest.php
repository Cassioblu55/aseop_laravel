<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\SettlementController;

class SettlementControllerTest extends TestCase
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
		$this->callSecure('GET', 'settlements/create');

		$this->assertResponseOk();

		$this->assertViewHas('settlement');
	}

	public function testEditShouldShowEditObjectPage(){
		$settlement = factory(\App\Settlement::class)->create();

		$this->callSecure('GET', 'settlements/'.$settlement->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('settlement');
	}

	public function testShowShouldShowShowObjectPage(){
		$settlement = factory(\App\Settlement::class)->create();

		$this->callSecure('GET', 'settlements/'.$settlement->id);

		$this->assertResponseOk();

		$this->assertViewHas('settlement');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'settlements/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){

		factory(\App\Settlement::class)->create(['name' => 'foo']);

		$response = $this->callSecure('GET', 'api/settlements');

		$this->assertResponseOk();

		$settlements = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($settlements));

		$settlement = $settlements[0];

		$expectedData = [
			"name" => 'foo',
			"population" => 52,
			"size" => 'S',
			'ruler_id' => 1,
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $settlement);
	}

}