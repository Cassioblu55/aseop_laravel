<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\MonsterController;
use App\Monster;

class MonsterControllerTest extends TestCase
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
		$this->callSecure('GET', 'monsters/create');

		$this->assertResponseOk();

		$this->assertViewHas('monster');
	}

	public function testEditShouldShowEditObjectPage(){
		$monster = factory(\App\Monster::class)->create();

		$this->callSecure('GET', 'monsters/'.$monster->id.'/edit');

		$this->assertResponseOk();

		$this->assertViewHas('monster');
	}

	public function testShowShouldShowShowObjectPage(){
		$monster = factory(\App\Monster::class)->create();

		$this->callSecure('GET', 'monsters/'.$monster->id);

		$this->assertResponseOk();

		$this->assertViewHas('monster');
	}

	public function testUploadShouldShowUploadPage(){
		$this->callSecure('GET', 'monsters/upload');

		$this->assertResponseOk();
	}

	public function testApiShouldRetrunAllData(){
		factory(\App\Monster::class)->create();

		$response = $this->callSecure('GET', 'api/monsters');

		$this->assertResponseOk();

		$monsters = json_decode($response->getContent(), true);

		$this->assertEquals(1, count($monsters));

		$monster = $monsters[0];

		$expectedData = [
			"armor" => 10,
			"hit_points" => "1d5+6",
			"speed" => 30,
			"xp" => 20,
			"challenge" => 1.5,
			"name" => "foo monster",
			"stats" => '{"strength":16,"dexterity":13,"constitution":13,"intelligence":7,"wisdom":11,"charisma":8}',

			'public' => "0",
			'owner_id' => Auth::user()->id,
			'approved' => "0"
		];

		$this->assertHashesHaveEqualValues($expectedData, $monster);
	}

	//TODO Correctly order tests  based on their appearance in the Controller
	public function testStoreShouldCreateNewMonster(){
		$monster = [
			"armor" => 10,
			"hit_points" => "1d5+6",
			"speed" => 30,
			"xp" => 20,
			"challenge" => 1.5,
			"name" => "foo monster",
			"stats" => '{"strength":16,"dexterity":13,"constitution":13,"intelligence":7,"wisdom":11,"charisma":8}',
		];


		$response = $this->call('POST', '/monsters', $monster);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/monsters/1?successMessage=Record+Added+Successfully'));

		$this->assertEquals(1, count(Monster::all()));

		$storedMonster = Monster::findById(1);
		$this->assertNotNull($storedMonster);

		$this->assertEquals(10, $storedMonster->armor);
		$this->assertEquals('1d5+6', $storedMonster->hit_points);
		$this->assertEquals(30, $storedMonster->speed);
		$this->assertEquals(20, $storedMonster->xp);
		$this->assertEquals(1.5, $storedMonster->challenge);
		$this->assertEquals("foo monster", $storedMonster->name);
		$this->assertEquals('{"strength":16,"dexterity":13,"constitution":13,"intelligence":7,"wisdom":11,"charisma":8}', $storedMonster->stats);

		$this->assertEquals(0, $storedMonster->approved);
		$this->assertEquals(0, $storedMonster->public);
		$this->assertEquals($this->user->id, $storedMonster->owner_id);
	}

	public function testStoreShouldNotCreateNewMonsterWhenMonsterInvalid(){
		$monster = [
			"armor" => 10,
			"hit_points" => "1d5+6",
			"speed" => 30,
			"xp" => 20,
			"challenge" => 1.5,
			"stats" => '{"strength":16,"dexterity":13,"constitution":13,"intelligence":7,"wisdom":11,"charisma":8}',
		];

		$response = $this->call('POST', '/monsters', $monster);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/monsters/create?errorMessage=Record+could+not+be+added'));

		$this->assertEquals(0, count(Monster::all()));
	}

	public function testUpdateShouldUpdateObject(){
		$monster = factory(Monster::class)->create();

		$newMonster = [
			"name" => "new monster name",
			'id' => $monster->id
		];

		$storedMonster = Monster::findById($monster->id);
		$this->assertEquals("foo monster", $storedMonster->name);

		$response = $this->call('PATCH', 'monsters/'.$monster->id, $newMonster);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/monsters/1?successMessage=Record+Updated+Successfully'));

		$storedMonster = Monster::findById($monster->id);
		$this->assertEquals("new monster name", $storedMonster->name);
	}

	public function testUpdateShouldNotUpdateIfObjectInvalid(){
		$monster = factory(Monster::class)->create();

		$newMonster = [
			'name' => null,
			'id' => $monster->id
		];

		$storedMonster = Monster::findById($monster->id);
		$this->assertEquals("foo monster", $storedMonster->name);

		$response = $this->call('PATCH', 'monsters/'.$monster->id, $newMonster);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/monsters/'.$monster->id.'/edit?errorMessage=Record+failed+to+update'));

		$storedMonster = Monster::findById($monster->id);
		$this->assertEquals("foo monster", $storedMonster->name);
	}

	public function testDestroyShouldDeleteRecord(){
		$monster = factory(Monster::class)->create();

		$count = count(Monster::all());

		$response = $this->call('DELETE', 'monsters/'.$monster->id);

		$this->assertEquals(302, $response->status());
		$this->assertRedirectedTo(url('/monsters?successMessage=Record+Deleted+Successfully'));

		$this->assertEquals($count-1, count(Monster::all()));
	}

}