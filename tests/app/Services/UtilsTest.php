<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Services\Utils;

class UtilsTest extends TestCase
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

    public function testGetRandomFromArrayShouldReturnRandomElementFromArray(){
    	$array = ["foo", "bar"];

	    $result = Utils::getRandomFromArray($array);

	    $this->assertTrue( $result== "foo" || $result == "bar");
    }

	public function testGetRandomKeyFromHashShouldReturnRandomKeyFromHash(){
		$hash = ["foo" => "bar", "bar" => "foo"];
		$result = Utils::getRandomKeyFromHash($hash);

		$this->assertTrue($result == "foo" || $result == "bar");
	}

	public function testGetBellCurveRangeShouldReturnNumberInBellCurveOfGivenData(){
		$configData = ['min' => 1, 'max' => 10, 'std' => 1];
		$bellNumber = Utils::getBellCurveRange($configData);

		$this->assertGreaterThanOrEqual(1, $bellNumber);
		$this->assertLessThanOrEqual(10, $bellNumber);
	}

}