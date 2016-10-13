<?php

namespace App\Console\Commands;

use App\Services\Logging;
use Illuminate\Console\Command;
use File;

class CreateTest extends GenericCommand
{
	private $logging;
	private $fullFilePath;
	private $path;

	const TEST_NAME_WITH_PATH = "testNameWithPath";

	const TEST_PATH = 'tests';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:aesopTest {testNameWithPath}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test class using template';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->logging = new Logging(self::class);
	    parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$this->path = $this->argument("testNameWithPath");
    	$this->setFilePathAndTestClassName();

		$pathWithFileNameRemoved = CommandUtils::getFileDirPath($this->fullFilePath);

	    File::makeDirectory($pathWithFileNameRemoved, 0775, true, true);

	    $this->createFileFromTemplate($this->fullFilePath, "test");

	    $usePath = "use ".str_replace("/", '\\',$this->path).";";


	    CommandUtils::replaceNames($this->fullFilePath, $usePath, '//use $usePath');

	    $this->comment($this->fullFilePath." created");
    }

    private function setFilePathAndTestClassName()
    {
	    $this->fullFilePath = self::TEST_PATH."/".$this->path."Test.php";

	    $pathSplit =  explode("/",$this->fullFilePath);

	    $testClassName = $pathSplit[count($pathSplit)-1];

	    $nameToReplace = explode(".",$testClassName)[0];

	    $this->setNameToReplace($nameToReplace);
    }

}
