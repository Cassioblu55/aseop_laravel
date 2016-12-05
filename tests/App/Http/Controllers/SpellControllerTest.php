<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\SpellController;
use App\Spell;

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

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewSpell(){
		$spell = [
			'name' => 'Test Name',
			'type' => 'abjuration',
			'class' => 'fighter',
			'level' => 6,
			'range' => 30,
			'description' => 'description',
		];


		$response = $this->call('POST', '/spells', $spell);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/spells/1?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(Spell::all()));

		$storedSpell = Spell::findById(1);
		$this->assertNotNull($storedSpell);

		$this->assertEquals('Test Name', $storedSpell->name);
		$this->assertEquals('abjuration', $storedSpell->type);
		$this->assertEquals('fighter', $storedSpell->class);
		$this->assertEquals(6, $storedSpell->level);
		$this->assertEquals(30, $storedSpell->range);
		$this->assertEquals('description', $storedSpell->description);

		$this->assertEquals(0, $storedSpell->approved);
		$this->assertEquals(0, $storedSpell->public);
		$this->assertEquals($this->user->id, $storedSpell->owner_id);
	}

	public function testStoreShouldNotCreateNewSpellWhenSpellInvalid(){
		$spell = [
			'type' => 'abjuration',
			'class' => 'fighter',
			'level' => 6,
			'range' => 30,
			'description' => 'description',
		];

		$response = $this->call('POST', '/spells', $spell);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/spells/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(Spell::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$spell = factory(Spell::class)->create();

		$newSpell = [
			'name' => 'New Spell Name',
			'id' => $spell->id
		];

		$storedSpell = Spell::findById($spell->id);
		$this->assertEquals("Test Name", $storedSpell->name);

		$response = $this->call('PATCH', 'spells/'.$spell->id, $newSpell);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/spells/1?successMessage=Record+Updated+Successfully'));

		$storedSpell = Spell::findById($spell->id);
		$this->assertEquals("New Spell Name", $storedSpell->name);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		self::ensureTrapOfIdOneExists();

		$spell = factory(Spell::class)->create();

		$newSpell = [
			'name' => null,
			'id' => $spell->id
		];

		$storedSpell = Spell::findById($spell->id);
		$this->assertEquals("Test Name", $storedSpell->name);

		$response = $this->call('PATCH', 'spells/'.$spell->id, $newSpell);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/spells/'.$spell->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedSpell = Spell::findById($spell->id);
		$this->assertEquals("Test Name", $storedSpell->name);
	}

	public function testDestroyShouldDeleteRecord(){
		$spell = factory(Spell::class)->create();

		$count = count(Spell::all());

		$response = $this->call('DELETE', 'spells/'.$spell->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/spells?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(Spell::all()));
	}

}