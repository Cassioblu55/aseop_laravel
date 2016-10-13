<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Console\Commands\CommandUtils;
use App\TestingUtils\FileTesting;

class CommandUtilsTest extends TestCase
{
    private $logging;

	const TESTING_TEMPLATE_PATH = "resources/assets/testing/txt/CommandUtils/TestTemplates";

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }

	public function testReplaceNamesShouldReplaceNameWithAnotherName(){
		$path = "resources/assets/testing/txt/CommandUtils/test_DO_NOT_EDIT.txt";
		$file = new FileTesting($path);

		$nameToReplace = "replace_with_this";
		$placeholder = "placeholder";

		$this->assertEquals($placeholder, $file->getFileContents());

		CommandUtils::replaceNames($path, $nameToReplace, $placeholder);

		$this->assertEquals($nameToReplace, $file->getFileContents());

		$file->revert();
	}

	public function testReplaceNamesShouldReplaceNameUsingBaseNameAsDefaultAndReplaceLower(){
		$path = "resources/assets/testing/txt/CommandUtils/base_name_test_DO_NOT_EDIT.txt";
		$file = new FileTesting($path);

		$nameToReplace = "Replace_with_this";
		$placeholder = "Base_name,base_name";

		$this->assertEquals($placeholder, $file->getFileContents());

		CommandUtils::replaceNames($path, $nameToReplace);

		$this->assertEquals("Replace_with_this,replace_with_this", $file->getFileContents());

		$file->revert();
	}

	public function testReplaceNamesShouldNotReplaceLowerCaseFileAToldNotTo(){
		$path = "resources/assets/testing/txt/CommandUtils/base_name_test_DO_NOT_EDIT.txt";
		$file = new FileTesting($path);

		$nameToReplace = "Replace_with_this";
		$placeholder = "Base_name,base_name";

		$this->assertEquals($placeholder, $file->getFileContents());

		CommandUtils::replaceNames($path, $nameToReplace, "Base_name", false);

		$this->assertEquals("Replace_with_this,base_name", $file->getFileContents());

		$file->revert();
	}

	public function testGetFileDirPathShouldReturnDirectoryPathOfFilePath(){
		$path = "foo/bar/file.txt";
		$this->assertEquals("foo/bar", CommandUtils::getFileDirPath($path));
	}

	public function testGetFileDirPathShouldReturnDirectoryPathOfFilePathWhenOnlyDirectories(){
		$path = "foo/bar";
		$this->assertEquals("foo/bar", CommandUtils::getFileDirPath($path));
	}

	public function testGetBaseFileContentsFromIdentifierShouldRetrunFileContentsFromIdentifer(){
		$fileContents = CommandUtils::getBaseFileContentsFromIdentifier("foo_DO_NOT_EDIT", true, self::TESTING_TEMPLATE_PATH);

		$this->assertEquals("blade template with Base_name, base_name", $fileContents);

		$fileContents = CommandUtils::getBaseFileContentsFromIdentifier("foo_DO_NOT_EDIT", false, self::TESTING_TEMPLATE_PATH);

		$this->assertEquals("template with Base_name, base_name", $fileContents);
	}

	public function testAddBaseFileContentsShouldAppendToTheBottomOfTheFile(){
		$fileToAddToPath = "resources/assets/testing/txt/CommandUtils/test_DO_NOT_EDIT.txt";
		$fileToAddTo = new FileTesting($fileToAddToPath);

		$fileToAddToContents = "placeholder";
		$fileToAddTo->assertFileContentsEqual($fileToAddToContents);

		$templateFile = new FileTesting("resources/assets/testing/txt/CommandUtils/TestTemplates/foo_DO_NOT_EDIT.blade.php");

		$templateFileContents = "blade template with Base_name, base_name";
		$templateFile->assertFileContentsEqual($templateFileContents);

		CommandUtils::addToFileFromIdentifier($fileToAddToPath, "foo_DO_NOT_EDIT", true, self::TESTING_TEMPLATE_PATH);

		$this->assertEquals($fileToAddToContents.$templateFileContents, $fileToAddTo->getFileContents());

		$fileToAddTo->revert();
	}

	public function testSetFileContentsFromIdentifierShouldSetFileContentsFromIdentifer(){
		$fileToSetContentPath = "resources/assets/testing/txt/CommandUtils/test_DO_NOT_EDIT.txt";
		$fileToSetContent = new FileTesting($fileToSetContentPath);

		$fileToSetContentContents = "placeholder";
		$fileToSetContent->assertFileContentsEqual($fileToSetContentContents);

		$templateFile = new FileTesting("resources/assets/testing/txt/CommandUtils/TestTemplates/foo_DO_NOT_EDIT.blade.php");

		$templateFileContents = "blade template with Base_name, base_name";
		$templateFile->assertFileContentsEqual($templateFileContents);

		CommandUtils::setFileContentsFromIdentifier($fileToSetContentPath, "foo_DO_NOT_EDIT", true, self::TESTING_TEMPLATE_PATH);

		$fileToSetContent->assertFileContentsEqual($templateFileContents);

		$fileToSetContent->revert();
	}

	public function testCreateFileFromTemplateShouldCreateNewFileFillContentsFromTemplateAndReplaceThePlaceHolderNames(){

		$newFilePath = "resources/assets/testing/txt/CommandUtils/newFile.php";

		$newFile = new FileTesting($newFilePath, true);

		$templateFile = new FileTesting("resources/assets/testing/txt/CommandUtils/TestTemplates/foo_DO_NOT_EDIT.blade.php");

		$templateFileContents = "blade template with Base_name, base_name";
		$templateFile->assertFileContentsEqual($templateFileContents);

		CommandUtils::createFileFromTemplate($newFilePath, "foo_DO_NOT_EDIT", "FooTest", "Base_name", true, true, self::TESTING_TEMPLATE_PATH);

		$this->assertTrue($newFile->exists());

		$this->assertEquals("blade template with FooTest, fooTest", $newFile->getFileContents());

		$newFile->revert();
	}

	public function testGetComposerCommandShouldReturnComposerCommand(){
		$shouldBeComposerCommand = env("COMPOSER", 'composer')." dump-autoload -q";
		$this->assertEquals($shouldBeComposerCommand, CommandUtils::getComposerCommand("dump-autoload -q"));
	}




}