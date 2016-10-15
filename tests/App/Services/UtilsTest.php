<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Services\Utils;
use App\NonPlayerCharacter;

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

	public function testEnsureNpcOfIdOneExistsShouldEnsureNpcOfIdOneExists(){
		$user = factory(\App\User::class)->create();
		$this->actingAs($user);

		NonPlayerCharacter::truncate();

		$count  = count(NonPlayerCharacter::all());

		$npc = NonPlayerCharacter::where("id", 1)->first();

		$this->assertNull($npc);

		self::ensureNpcOfIdOneExists();

		$npc = NonPlayerCharacter::where("id", 1);

		$this->assertNotNull($npc);

		$this->assertEquals($count+1, count(NonPlayerCharacter::all()));

		$this->actingAs(new \App\User());
	}

	public function testEnsureTrapOfIdOneExistsShouldEnsureNpcOfIdOneExists(){
		$user = factory(\App\User::class)->create();
		$this->actingAs($user);

		\App\Trap::truncate();

		$count  = count(\App\Trap::all());

		$trap = \App\Trap::where("id", 1)->first();

		$this->assertNull($trap);

		self::ensureTrapOfIdOneExists();

		$trap = \App\Trap::where("id", 1);

		$this->assertNotNull($trap);

		$this->assertEquals($count+1, count(\App\Trap::all()));

		$this->actingAs(new \App\User());
	}

	public function testAssertHashesHaveEqualValuesShouldNotFailWhenTwoArraysAreEqual(){
		$arrayOne = ['a' => "b","c"=>"b","u"=>"p"];
		$arrayTwo = ['a' => "b","c"=>"b","u"=>"p"];

		$this->assertHashesHaveEqualValues($arrayOne, $arrayTwo);
	}

	public function testGetLetterByNumberShouldReturnLetterGivenNumber(){
		$this->assertEquals("A", Utils::getLetterByNumber(0));

		$this->assertEquals("H", Utils::getLetterByNumber(7));

		$this->assertEquals("Z", Utils::getLetterByNumber(25));

		$this->assertNull(Utils::getLetterByNumber(26));
	}


}