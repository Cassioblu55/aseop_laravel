<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Stats;

class StatsTest extends TestCase
{
    private $logging;

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }

    public function setUp(){
        parent::setUp();
        //My custom setup code
    }

    public function tearDown()
    {
        //My custom tear down code
        parent::tearDown();
    }

	public function testValidStatsArrayShouldReturnTrueWithValidStatsArray(){
    	$validStats= '{"strength":16,"dexterity":13,"constitution":0,"intelligence":7,"wisdom":11,"charisma":8}';
	    $this->assertTrue(Stats::validStatsArray($validStats));

	    $invalidStats = 'invalid';
	    $this->assertFalse(Stats::validStatsArray($invalidStats));

	    $invalidStatsMissingStat = '{"strength":16,"dexterity":13,"constitution":13,"intelligence":7,"wisdom":11}';
	    $this->assertFalse(Stats::validStatsArray($invalidStatsMissingStat));

	    $invalidStatsStatLessThenZero = '{"strength":-6,"dexterity":13,"constitution":13,"intelligence":7,"wisdom":11,"charisma":8}';
	    $this->assertFalse(Stats::validStatsArray($invalidStatsStatLessThenZero));

		$invalidStatsStatFloat = '{"strength":6.6,"dexterity":13,"constitution":13,"intelligence":7,"wisdom":11,"charisma":8}';
		$this->assertFalse(Stats::validStatsArray($invalidStatsStatFloat));

		$invalidStatsExtraStat = '{"strength":16,"dexterity":13,"constitution":0,"intelligence":7,"wisdom":11,"charisma":8, "foo":"bar"}';
		$this->assertFalse(Stats::validStatsArray($invalidStatsExtraStat));

	    $invalidStatsStatNotInteger = '{"strength":"foo bar","dexterity":13,"constitution":13,"intelligence":7,"wisdom":11,"charisma":8}';
	    $this->assertFalse(Stats::validStatsArray($invalidStatsStatNotInteger));
    }


}