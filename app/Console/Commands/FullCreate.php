<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class FullCreate extends GenericCommand
{
	//private $modelName;
	private $viewDirectory;
	private $createdDirectoryPath;

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
	    $this->viewDirectory = base_path('resources/views/');
	    parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$this->setNameToReplaceFromIdentifier('modelName');

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
	    Artisan::call('make:controller', ['name' =>$this->nameToReplace."Controller" ,'--resource'=>true]);

	    $controllerPath = base_path('app/Http/Controllers/'.$this->nameToReplace.'Controller.php');
	    CommandUtils::setFileContentsFromIdentifier($controllerPath, 'controller');
	    $this->replaceNames($controllerPath);
    }

    private function addRoutes(){
    	$apiPath = base_path('routes/api.php');

	    CommandUtils::addToFileFromIdentifier($apiPath, 'api');
	    $this->replaceNames($apiPath);

	    $webPath = base_path('routes/web.php');

	    CommandUtils::addToFileFromIdentifier($webPath, 'web');
	    $this->replaceNames($webPath);
    }

    private function createModelAndMigration(){
	    Artisan::call('make:model', ['name' =>$this->nameToReplace, '-m'=>true]);

	    $modelPath = base_path('app/'.$this->nameToReplace.'.php');
	    CommandUtils::setFileContentsFromIdentifier($modelPath, 'model');
	    $this->replaceNames($modelPath);
    }

    private function createViewDirectoryReturnSuccess(){
	    $createdDirectoryPath = $this->viewDirectory.lcfirst($this->nameToReplace).'s';
	    $createSuccessful =  File::makeDirectory($createdDirectoryPath);
	    if($createSuccessful){
	    	$this->createdDirectoryPath = $createdDirectoryPath;
		    $this->comment($createdDirectoryPath." made successfully");
	    }
    	return $createSuccessful;
    }

    private function createIndexPage(){
	    $indexPath = $this->getFilePathFromPrefixSuffix($this->createdDirectoryPath, "index");
	    $this->createFileFromTemplate($indexPath, "index");
	    $this->comment($indexPath . " made successfully");
    }

    private function createEditPage()
    {
	    $editPath = $this->getFilePathFromPrefixSuffix($this->createdDirectoryPath, "edit");
	    $this->createFileFromTemplate($editPath, "edit");
	    $this->comment($editPath . " made successfully");
    }

}
