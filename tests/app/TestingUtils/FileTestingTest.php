<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\TestingUtils\FileTesting;

class FileTestingTest extends TestCase
{
    use DatabaseTransactions;

	const BASE_FILE_PATH = "resources/assets/testing/txt/FileTesting";

    private $logging;

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }

    public function testFileTestingShouldOnlyAllowTestingOnExistingFile(){
    	$path = self::BASE_FILE_PATH."/text_DO_NOT_EDIT.txt";

	    $file = new FileTesting($path);

	    self::assertFileExists($path);

	    $this->assertEquals($path, $file->getFilePath());

	    $startingContents = "foo bar";

	    $this->assertEquals($startingContents, $file->getStartingText());
    }

	public function testFileTestingShouldEnsureFileDoesNotExistWhenToldSo(){
		$path = self::BASE_FILE_PATH."/newFile.txt";

		self::assertFileNotExists($path);
		$file = new FileTesting($path, true);

		$this->assertEquals($path, $file->getFilePath());

		$this->assertNull($file->getStartingText());
	}

	public function testFileTestingShouldAcceptDirectoryPaths(){
		$path = self::BASE_FILE_PATH."/testDirectory";

		$this->assertFileExists($path);

		$file = new FileTesting($path);

		$this->assertTrue($file->exists());

		$this->assertFalse($file->isFile());

		$this->assertNull($file->getFileContents());

	}

	public function testFileTestingShouldAcceptDirectoryPathsForNonExistingDirectories(){
		$path = self::BASE_FILE_PATH."/testDirectoryThatDoesNotExist";

		$this->assertFileNotExists($path);

		$file = new FileTesting($path, true);

		$this->assertFalse($file->exists());

		$this->assertFalse($file->isFile());

		$this->assertNull($file->getFileContents());
	}

    public function testGetFileContentsShouldReturnFileContents(){
	    $path = self::BASE_FILE_PATH."/text_DO_NOT_EDIT.txt";
	    $file = new FileTesting($path);

	    $this->assertEquals("foo bar", $file->getFileContents());

	    file_put_contents($path, "foo bars");

	    $this->assertEquals("foo bars", $file->getFileContents());

	    file_put_contents($path, "foo bar");
    }


	public function testGetFileContentsShouldReturnNullIfFileDoesNotExist(){
		$path = self::BASE_FILE_PATH."/newFile.txt";

		self::assertFileNotExists($path);
		$file = new FileTesting($path, true);

		$this->assertNull($file->getFileContents());
	}

	public function testExistsShouldReturnTrueIfFileExists(){
		$path = self::BASE_FILE_PATH."/text_DO_NOT_EDIT.txt";

		self::assertFileExists($path);

		$file = new FileTesting($path);
		$this->assertTrue($file->exists());
	}

	public function testExistsShouldReturnFalseIfFileDoesNotExist(){
		$path = self::BASE_FILE_PATH."/newFile.txt";

		self::assertFileNotExists($path);

		$file = new FileTesting($path, true);
		$this->assertFalse($file->exists());
	}

    public function testRevertShouldSetFileContentsBackToStartingContents(){
	    $path = self::BASE_FILE_PATH."/text_DO_NOT_EDIT.txt";
	    $file = new FileTesting($path);

	    $this->assertEquals("foo bar", $file->getFileContents());

	    file_put_contents($path, "foo bars");
	    $this->assertEquals("foo bars", $file->getFileContents());

	    $file->revert();

	    $this->assertEquals("foo bar", $file->getFileContents());
    }

	public function testRevertShouldDeleteFileIfNewFileExists(){
		$path = self::BASE_FILE_PATH."/newFile.txt";

		self::assertFileNotExists($path);

		$file = new FileTesting($path, true);

		fopen($path, 'w');

		self::assertFileExists($path);

		$file->revert();

		self::assertFileNotExists($path);
	}

	public function testRevertShouldRemoveDirectoryWhenToldNewDirectoryMade(){
		$path = self::BASE_FILE_PATH."/testDirectoryThatDoesNotExist";

		$this->assertFileNotExists($path);

		$file = new FileTesting($path, true);

		File::makeDirectory($path);

		self::assertFileExists($path);

		$file->revert();

		self::assertFileNotExists($path);
	}

}