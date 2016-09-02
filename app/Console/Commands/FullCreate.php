<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FullCreate extends Command
{
	private $modelName;
	private $viewDirectory;
	private $createdDirectoryPath;

	const BASE_FILE_PATH = '/resources/views/layout/base';

	/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:full {modelName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create model view controllers and migration';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$this->modelName = $this->argument('modelName');
	    $this->viewDirectory = base_path('resources/views/');

		$this->createController();

	    $this->addRoutes();

	    $this->createModelAndMigration();
	    $this->comment("Model, migration and controller made successfully");

	    $createFileSuccess = $this->createViewDirectoryReturnSuccess();

	    if($createFileSuccess){
		    $this->createIndexPage();
			$this->createEditPage();
	    }
    }

    private function createController(){
	    Artisan::call('make:controller', ['name' =>$this->modelName."Controller" ,'--resource'=>true]);

	    $controllerPath = base_path('app/Http/Controllers/'.$this->modelName.'Controller.php');
	    $this->addBaseFileContents($controllerPath, 'controller');
	    $this->replaceNames($controllerPath);
    }

    private function addRoutes(){
    	$apiPath = base_path('routes/api.php');

		$this->addFilesFromIdentifer($apiPath, 'api');
	    $this->replaceNames($apiPath);

	    $webPath = base_path('routes/web.php');

	    $this->addFilesFromIdentifer($webPath, 'web');
	    $this->replaceNames($webPath);
    }

    private function addFilesFromIdentifer($filePathToAppend, $baseIdentifer){
    	$sourceContent = file_get_contents($filePathToAppend);
	    $contentToAppend = $this->getBaseFileContentsFromIdentifer($baseIdentifer);

	    file_put_contents($filePathToAppend, $sourceContent.$contentToAppend);
    }


    private function createModelAndMigration(){
	    Artisan::call('make:model', ['name' =>$this->modelName, '-m'=>true]);

	    $modelPath = base_path('app/'.$this->modelName.'.php');
	    $this->addBaseFileContents($modelPath, 'model');
	    $this->replaceNames($modelPath);
    }

    private function createViewDirectoryReturnSuccess(){
	    $createdDirectoryPath = $this->viewDirectory.lcfirst($this->modelName).'s';
	    $createSuccessful =  File::makeDirectory($createdDirectoryPath);
	    if($createSuccessful){
	    	$this->createdDirectoryPath = $createdDirectoryPath;
		    $this->comment($createdDirectoryPath." made successfully");
	    }
    	return $createSuccessful;
    }

    private function createIndexPage(){
	    $indexPath = $this->createdDirectoryPath."/".lcfirst($this->modelName)."_index.blade.php";
	    $file = fopen($indexPath, 'w');
	    if($file){
	    	$this->addBaseFileContents($indexPath, 'index');
		    $this->replaceNames($indexPath);
		    $this->comment($indexPath." made successfully");
	    }
    }

    private function addBaseFileContents($destinationFile, $baseIdentifier){
	    $data = $this->getBaseFileContentsFromIdentifer($baseIdentifier);
    	file_put_contents($destinationFile, $data);
    }

    private function getBaseFileContentsFromIdentifer($baseIdentifer){
    	$fileName = $baseIdentifer.".blade.php";
    	return file_get_contents(base_path(self::BASE_FILE_PATH."/".$fileName));
    }

    private function replaceNames($filePath){
	    $str=file_get_contents($filePath);

	    $str=str_replace("Base_name", $this->modelName,$str);
	    $str=str_replace("base_name", lcfirst($this->modelName),$str);

	    file_put_contents($filePath, $str);
    }

    private function createEditPage(){
	    $editPath = $this->createdDirectoryPath."/".lcfirst($this->modelName)."_edit.blade.php";
	    $file = fopen($editPath, 'w');
	    if($file){
		    $this->addBaseFileContents($editPath, 'edit');
		    $this->replaceNames($editPath);
		    $this->comment($editPath." made successfully");
	    }
    }

}
