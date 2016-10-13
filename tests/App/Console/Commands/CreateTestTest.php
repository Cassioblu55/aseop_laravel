<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\TestingUtils\FileTesting;

class CreateTestTest extends TestCase
{
    use DatabaseTransactions;

    private $logging;

	private $testFileToBeCreated;
	private $testFileToBeCreatedWithSubdirectories;
	private $testDirectoryToBeCreated;

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);

	    $fileToBeCreatedPath = "tests/FooTest.php";
	    $this->testFileToBeCreated = new FileTesting($fileToBeCreatedPath, true);

	    $fileToBeCreatedPath = "tests/fooTest/FooTest.php";
	    $this->testFileToBeCreatedWithSubdirectories = new FileTesting($fileToBeCreatedPath, true);

	    $directoryToBeCreatedPath = "tests/fooTest";
	    $this->testDirectoryToBeCreated = new FileTesting($directoryToBeCreatedPath, true);

	    parent::__construct();
    }

	public function setUp(){
		parent::setUp();

		$this->assertFalse($this->testFileToBeCreated->exists());
		$this->assertFalse($this->testFileToBeCreatedWithSubdirectories->exists());
		$this->assertFalse($this->testDirectoryToBeCreated->exists());
	}

	public function tearDown()
	{
		$this->testFileToBeCreated->revert();
		$this->testFileToBeCreatedWithSubdirectories->revert();
		$this->testDirectoryToBeCreated->revert();

		parent::tearDown();
	}


    public function testCreateTestShouldCreateTestFile(){

	    $fileTemplatePath = "resources/automation/templates/test.blade.php";
	    $fileTemplate = new FileTesting($fileTemplatePath);

	    $this->assertTrue($fileTemplate->exists());

	    Artisan::call("make:aesopTest", ["testNameWithPath" => 'Foo']);

	    $this->assertTrue($this->testFileToBeCreated->exists());

	    $fileToBeCreatedExpectedContent = str_replace("Base_name", "FooTest", $fileTemplate->getFileContents());

	    $fileToBeCreatedExpectedContent = str_replace('//use $usePath', 'use Foo;', $fileToBeCreatedExpectedContent);

	    $this->assertEquals($fileToBeCreatedExpectedContent, $this->testFileToBeCreated->getFileContents());
    }

	public function testCreateTestShouldMakeDirectoryPath(){

		$fileTemplatePath = "resources/automation/templates/test.blade.php";
		$fileTemplate = new FileTesting($fileTemplatePath);

		$this->assertTrue($fileTemplate->exists());

		Artisan::call("make:aesopTest", ["testNameWithPath" => 'fooTest/Foo']);

		$this->assertTrue($this->testFileToBeCreatedWithSubdirectories->exists());
		$this->assertTrue($this->testFileToBeCreatedWithSubdirectories->isFile());

		$this->assertTrue($this->testDirectoryToBeCreated->exists());
		$this->assertFalse($this->testDirectoryToBeCreated->isFile());

		$fileToBeCreatedExpectedContent = str_replace("Base_name", "FooTest", $fileTemplate->getFileContents());

		$fileToBeCreatedExpectedContent = str_replace('//use $usePath', 'use fooTest\Foo;', $fileToBeCreatedExpectedContent);

		$this->assertEquals($fileToBeCreatedExpectedContent, $this->testFileToBeCreatedWithSubdirectories->getFileContents());
	}

}