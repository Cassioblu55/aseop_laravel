<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\SettlementTraitController;
use App\SettlementTrait;

class SettlementTraitControllerTest extends TestCase
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
		$this->callSecure('GET', 'settlementTraits/create');

		$this->assertResponseOk();

		$this->assertViewHas('settlementTrait');
	}

	public function testEditShouldShowEditObjectPage(){
		$settlementTrait = factory(\App\SettlementTrait::class)->create();

		$this->callSecure('GET', 'settlementTraits/'.$settlementTrait->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('settlementTrait');
	}

	public function testShowShouldShowShowObjectPage(){
		$settlementTrait = factory(\App\SettlementTrait::class)->create();

		$this->callSecure('GET', 'settlementTraits/'.$settlementTrait->id);

		$this->assertResponseOk();

		$this->assertViewHas('settlementTrait');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'settlementTraits/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\SettlementTrait::class)->create();

		$response = $this->callSecure('GET', 'api/settlementTraits');

		$this->assertResponseOk();

		$settlementTraits = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($settlementTraits));

		$settlementTrait = $settlementTraits[0];

		$expectedData = [
			'type' => 'name',
			'trait' => 'Coolsville',
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $settlementTrait);
	}

}