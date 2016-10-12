<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\TestingUtils\FileTesting;
use Ouzo\Utilities\Clock;
use App\Console\Commands\CommandUtils;

class FullCreateTest extends TestCase
{
    use DatabaseTransactions;

    private $logging;
	private $arrayOfFileTesting = [];

	const MODEL_FILE_PATH = "app/Foo.php";
	const CONTROLLER_FILE_PATH ="app/Http/Controllers/FooController.php";
	const VIEW_DIRECTORY_PATH = "resources/views/foos";
	const INDEX_FILE_PATH = "resources/views/foos/foo_index.blade.php";
	const EDIT_FILE_PATH = "resources/views/foos/foo_edit.blade.php";

	const DB_SEARCH_PATH = "database/migrations/*_create_foos_table.php";

	const FILES_TO_BE_CREATED = [self::MODEL_FILE_PATH, self::CONTROLLER_FILE_PATH, self::VIEW_DIRECTORY_PATH, self::INDEX_FILE_PATH, self::EDIT_FILE_PATH];

	const API_FILE_PATH = "routes/api.php";
	const WEB_FILE_PATH = "routes/web.php";

	const FILES_TO_BE_MODIFED = [self::API_FILE_PATH, self::WEB_FILE_PATH];


    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);

	    foreach (self::FILES_TO_BE_CREATED as $filePath){
		    $fileToBeCreated = new FileTesting($filePath, true);
		    array_push($this->arrayOfFileTesting, $fileToBeCreated);
	    }

	    foreach (self::FILES_TO_BE_MODIFED as $filePath){
		    $fileToBeAddedTo = new FileTesting($filePath);
		    array_push($this->arrayOfFileTesting, $fileToBeAddedTo);
	    }

        parent::__construct();
    }

    public function tearDown()
    {
	    $arrayOfDirectories = [];
	    foreach ($this->arrayOfFileTesting as $item){
		    if($item->isFile()){
			    $item->revert();
		    }else{
			    array_push($arrayOfDirectories, $item);
		    }
	    }
	    foreach($arrayOfDirectories as $dir){
		    $dir->revert();
	    }

	    foreach (glob(self::DB_SEARCH_PATH) as $migrationFile){
		    $migrationFile = new FileTesting($migrationFile);
		    $migrationFile->deleteFile();
	    }

	    CommandUtils::composer("dump-autoload -q");

	    parent::tearDown();
    }


	public function testFullCreateHandleCreatesAllRequiredDocumentsAndDirectories(){

    	$templateResourcePath = "resources/automation/templates";

		$arrayOfTemplatesToBeUsed = [
		    self::MODEL_FILE_PATH => $templateResourcePath."/model.blade.php",
		    self::CONTROLLER_FILE_PATH => $templateResourcePath."/controller.blade.php",

		    self::INDEX_FILE_PATH => $templateResourcePath."/index.blade.php",
		    self::EDIT_FILE_PATH => $templateResourcePath."/edit.blade.php",

		    self::API_FILE_PATH => $templateResourcePath."/api.blade.php",
		    self::WEB_FILE_PATH => $templateResourcePath."/web.blade.php",
	    ];

	    Artisan::call("make:full", ["modelName" => 'Foo']);

	    foreach ($this->arrayOfFileTesting as $item){
		    $this->assertTrue($item->exists(), $item->getFilePath()." does not exist");

		    if($item->isFile()){
			    $filePath = $item->getFilePath();
			    $templateContent = file_get_contents($arrayOfTemplatesToBeUsed[$filePath]);

			    $templateContent = str_replace("Base_name", "Foo", $templateContent);
			    $templateContent = str_replace("base_name", "foo", $templateContent);

			    $this->assertContains($templateContent, $item->getFileContents());
		    }
	    }

	    $arrayOfCreatedMigrationFiles =  glob(self::DB_SEARCH_PATH);

	    $this->assertEquals(1, count($arrayOfCreatedMigrationFiles));

	    $migrationFile = new FileTesting($arrayOfCreatedMigrationFiles[0]);
	    $this->assertTrue($migrationFile->exists());

	    $this->assertContains('Schema::create(\'foos\', function (Blueprint $table) {', $migrationFile->getFileContents());

    }

}