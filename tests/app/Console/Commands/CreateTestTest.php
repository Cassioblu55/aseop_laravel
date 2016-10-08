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

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }


    public function testCreateTestShouldCreateTestFile(){

    	$fileToBeCreatedPath = "tests/FooTest.php";

	    $fileToBeCreated = new FileTesting($fileToBeCreatedPath, true);

	    $this->assertFalse($fileToBeCreated->exists());

	    $fileTemplatePath = "resources/automation/templates/test.blade.php";
	    $fileTemplate = new FileTesting($fileTemplatePath);

	    $this->assertTrue($fileTemplate->exists());

	    Artisan::call("make:aesopTest", ["testNameWithPath" => 'Foo']);

	    $this->assertTrue($fileToBeCreated->exists());

	    $fileToBeCreatedExpectedContent = str_replace("Base_name", "FooTest", $fileTemplate->getFileContents());

	    $this->assertEquals($fileToBeCreatedExpectedContent, $fileToBeCreated->getFileContents());

	    $fileToBeCreated->revert();
    }

	public function testCreateTestShouldMakeDirectoryPath(){

		$fileToBeCreatedPath = "tests/fooTest/FooTest.php";
		$fileToBeCreated = new FileTesting($fileToBeCreatedPath, true);
		$this->assertFalse($fileToBeCreated->exists());

		$directoryToBeCreatedPath = "tests/fooTest";
		$directoryToBeCreated = new FileTesting($directoryToBeCreatedPath, true);
		$this->assertFalse($directoryToBeCreated->exists());

		$fileTemplatePath = "resources/automation/templates/test.blade.php";
		$fileTemplate = new FileTesting($fileTemplatePath);

		$this->assertTrue($fileTemplate->exists());

		Artisan::call("make:aesopTest", ["testNameWithPath" => 'fooTest/Foo']);

		$this->assertTrue($fileToBeCreated->exists());
		$this->assertTrue($fileToBeCreated->isFile());

		$this->assertTrue($directoryToBeCreated->exists());
		$this->assertFalse($directoryToBeCreated->isFile());

		$fileToBeCreatedExpectedContent = str_replace("Base_name", "FooTest", $fileTemplate->getFileContents());

		$this->assertEquals($fileToBeCreatedExpectedContent, $fileToBeCreated->getFileContents());

		$fileToBeCreated->revert();
		$directoryToBeCreated->revert();
	}

}