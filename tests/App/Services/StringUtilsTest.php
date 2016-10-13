<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Services\StringUtils;


class StringUtilsTest extends TestCase
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

    public function testDisplayShouldReplaceUnderscoreStringWithSpacesAndUppercase(){
    	$this->assertEquals("Foo Bar", StringUtils::display("foo_bar"));
    }

    public function testIsEmptyJSONShouldRetrunTrueWhenJSONStringIsEmpty(){
    	$this->assertTrue(StringUtils::isEmptyJson(new stdClass()));
    }

	public function testIsEmptyJSONShouldReturnFalseWhenJSONStringIsNotEmpty(){
		$this->assertFalse(StringUtils::isEmptyJson("{foo:bar}"));
	}

}