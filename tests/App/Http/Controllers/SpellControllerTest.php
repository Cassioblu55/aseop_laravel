<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\SpellController;

class SpellControllerTest extends TestCase
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
		$this->callSecure('GET', 'spells/create');

		$this->assertResponseOk();

		$this->assertViewHas('spell');
	}

	public function testEditShouldShowEditObjectPage(){
		$spell = factory(\App\Spell::class)->create();

		$this->callSecure('GET', 'spells/'.$spell->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('spell');
	}

	public function testShowShouldShowShowObjectPage(){
		$spell = factory(\App\Spell::class)->create();

		$this->callSecure('GET', 'spells/'.$spell->id);

		$this->assertResponseOk();

		$this->assertViewHas('spell');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'spells/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\Spell::class)->create();

		$response = $this->callSecure('GET', 'api/spells');

		$this->assertResponseOk();

		$spells = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($spells));

		$spell = $spells[0];

		$expectedData = [
			'name' => 'Test Name',
			'type' => 'abjuration',
			'class' => 'fighter',
			'level' => 6,
			'range' => 30,
			'description' => 'description',
			
			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $spell);
	}

}