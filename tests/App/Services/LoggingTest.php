<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Services\Logging;

class LoggingTest extends TestCase
{
    private $logging;
	private $logFile;

	private $testLog;

	private $defaultLogging;

	const LOG_FILE_PATH = "storage/logs/laravel.log";

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);

	    $this->logFile = new \App\TestingUtils\FileTesting(self::LOG_FILE_PATH);

	    $this->testLog = new Logging("testClass");

	    $this->defaultLogging = env("LOG");

	    parent::__construct();
    }

    public function setUp(){
        parent::setUp();

	    putenv("LOG=true");
	    $this->logFile->clear();
    }

    public function tearDown()
    {
	    $this->logFile->revert();

	    putenv("LOG=".$this->defaultLogging);

        parent::tearDown();
    }

    public function testLogErrorShouldLogError(){

    	$this->assertEquals("", $this->logFile->getFileContents());

	    $this->testLog->logError("foo bar");

	    $expectedResult = "testing.ERROR: testClass: foo bar";

	    $this->assertContains($expectedResult, $this->logFile->getFileContents());
    }

	public function testLogWarningShouldLogWarning(){

		$this->assertEquals("", $this->logFile->getFileContents());

		$this->testLog->logWarning("foo bar");

		$expectedResult = "testing.WARNING: testClass: foo bar";

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

	public function testLogInfoShouldLogInfo(){

		$this->assertEquals("", $this->logFile->getFileContents());

		$this->testLog->logInfo("foo bar");

		$expectedResult = "testing.INFO: testClass: foo bar";

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

	public function testLogErrorShouldLogErrorShouldNotIncludeClassNameWhenNotToldTo(){

		$this->assertEquals("", $this->logFile->getFileContents());

		$this->testLog->logError("foo bar", true);

		$expectedResult = "testing.ERROR: foo bar";

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

	public function testLogErrorShouldNotLogIfLoggingIsTurnedOff(){
		putenv("LOG=false");

		$this->assertEquals("", $this->logFile->getFileContents());

		$this->testLog->logError("foo bar");

		$this->assertEquals("", $this->logFile->getFileContents());
	}

	public function testLogErrorShouldLogIfLoggingIsTurnedOffButAlwaysLogIsOn(){
		putenv("LOG=false");

		$this->assertEquals("", $this->logFile->getFileContents());

		$this->testLog->logError("foo bar", false, true);

		$expectedResult = "testing.ERROR: testClass: foo bar";

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

	public function testLogJsonShouldLogJsonData(){
		$this->assertEquals("", $this->logFile->getFileContents());

		$this->testLog->logJson('{"foo":"bar"}');

		$expectedResult = 'testing.INFO: testClass: {"foo":"bar"}';

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

	public function testLogJsonShouldLogErrorIsJsonNotGiven(){
		$this->assertEquals("", $this->logFile->getFileContents());

		$this->testLog->logJson("{not a valid json");

		$expectedResult = 'testing.INFO: testClass: Object could not be converted to json.';

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

	public function testLogJsonShouldLogObjectAsJson(){
		$this->assertEquals("", $this->logFile->getFileContents());

		$object = new stdClass();
		$object->foo = "bar";

		$this->testLog->logJson($object);

		$expectedResult = 'testing.INFO: testClass: {"foo":"bar"}';

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

	public function testPingShouldAddPingToLog(){
		$this->assertEquals("", $this->logFile->getFileContents());

		$this->testLog->ping();

		$expectedResult = 'testing.INFO: testClass: ping';

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

	public function testLogShouldWriteToLog(){
		$this->assertEquals("", $this->logFile->getFileContents());

		Logging::log("foo bar", "testClass");

		$expectedResult = 'testing.INFO: testClass: foo bar';

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

	public function testErrorShouldWriteToLog(){
		$this->assertEquals("", $this->logFile->getFileContents());

		Logging::error("foo bar", "testClass");

		$expectedResult = 'testing.ERROR: testClass: foo bar';

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

	public function testWarningShouldWriteToLog(){
		$this->assertEquals("", $this->logFile->getFileContents());

		Logging::warning("foo bar", "testClass");

		$expectedResult = 'testing.WARNING: testClass: foo bar';

		$this->assertContains($expectedResult, $this->logFile->getFileContents());
	}

}