<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\ForestEncounter;

class RandomTest extends TestCase
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

    public function testRandomShouldReturnRandomRow(){
	    ForestEncounter::truncate();

    	factory(ForestEncounter::class)->create();
	    factory(ForestEncounter::class)->create([
    	    "title" => 'other title'
	    ]);

	    for($i=0; $i<10; $i++){
	    	$forestEncounter = ForestEncounter::random();
		    $this->assertTrue($forestEncounter->title == "title" || $forestEncounter->title == "other title");
	    }
    }

}