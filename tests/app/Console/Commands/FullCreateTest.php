<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\TestingUtils\FileTesting;
use Ouzo\Utilities\Clock;

class FullCreateTest extends TestCase
{
    use DatabaseTransactions;

    private $logging;
	private $arrayOfFileTesting;

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }

	public function testFullCreateHandleCreatesAllRequiredDocumentsAndDirectories(){

    	$templateResourcePath = "resources/automation/templates";

	    $modelFilePath = "app/Foo.php";
	    $controllerFilePath = "app/Http/Controllers/FooController.php";
	    $viewDirectoryFilePath = "resources/views/foos";
	    $indexFilePath = "resources/views/foos/foo_index.blade.php";
	    $editFilePath =  "resources/views/foos/foo_edit.blade.php";

	    $arrayOfFilesToBeCreated = [
		    $modelFilePath,
		    $controllerFilePath,

		    $viewDirectoryFilePath,
		    $indexFilePath,
		    $editFilePath
	    ];

	    $apiFilePath = "routes/api.php";
	    $webFilePath = "routes/web.php";

	    $arrayOfFilesToBeAddedTo = [
            $apiFilePath,
		    $webFilePath
	    ];

	    $arrayOfFileTesting = [];
	    foreach ($arrayOfFilesToBeCreated as $filePath){
	    	$fileToBeCreated = new FileTesting($filePath, true);
		    $this->assertFalse($fileToBeCreated->exists());
	    	array_push($arrayOfFileTesting, $fileToBeCreated);
	    }

	    foreach ($arrayOfFilesToBeAddedTo as $filePath){
	    	$fileToBeAddedTo = new FileTesting($filePath);
		    $this->assertTrue($fileToBeAddedTo->exists());
		    array_push($arrayOfFileTesting, $fileToBeAddedTo);
	    }

	    $arrayOfTemplatesToBeUsed = [
		    $modelFilePath => $templateResourcePath."/model.blade.php",
		    $controllerFilePath => $templateResourcePath."/controller.blade.php",

		    $indexFilePath => $templateResourcePath."/index.blade.php",
		    $editFilePath => $templateResourcePath."/edit.blade.php",

		    $apiFilePath => $templateResourcePath."/api.blade.php",
		    $webFilePath => $templateResourcePath."/web.blade.php",
	    ];

		$this->arrayOfFileTesting = $arrayOfFileTesting;

	    Artisan::call("make:full", ["modelName" => 'Foo']);

	    foreach ($arrayOfFileTesting as $item){
		    $this->assertTrue($item->exists(), $item->getFilePath()." does not exist");

		    if($item->isFile()){
			    $filePath = $item->getFilePath();
			    $templateContent = file_get_contents($arrayOfTemplatesToBeUsed[$filePath]);

			    $templateContent = str_replace("Base_name", "Foo", $templateContent);
			    $templateContent = str_replace("base_name", "foo", $templateContent);

			    $this->assertContains($templateContent, $item->getFileContents());
		    }
	    }

	    $dbMigrationPathSearchTerm = "database/migrations/*_create_foos_table.php";
	    $arrayOfMigrationFiles = glob($dbMigrationPathSearchTerm);

	    $this->assertEquals(1, count($arrayOfMigrationFiles));

	    $migrationFile = new FileTesting($arrayOfMigrationFiles[0]);
	    $this->assertTrue($migrationFile->exists());

	    $this->assertContains('Schema::create(\'foos\', function (Blueprint $table) {', $migrationFile->getFileContents());

		//Test tear down
		//TODO: move this so it is called even if test fails

		$arrayOfDirectories = [];
		foreach ($arrayOfFileTesting as $item){
			if($item->isFile()){
				$item->revert();
			}else{
				array_push($arrayOfDirectories, $item);
			}
		}
		foreach($arrayOfDirectories as $dir){
			$dir->revert();
		}

		foreach ($arrayOfMigrationFiles as $migrationFile){
			$migrationFile = new FileTesting($migrationFile);
			$migrationFile->deleteFile();
		}
			shell_exec('composer dump-autoload -q');
    }

}