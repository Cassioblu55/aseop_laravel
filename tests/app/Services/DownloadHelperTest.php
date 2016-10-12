<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Services\DownloadHelper;

class DownloadHelperTest extends TestCase
{
    private $logging;

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }

    public function setUp(){
        parent::setUp();
        //My custom setup code
    }

    public function tearDown()
    {
        //My custom tear down code
        parent::tearDown();
    }

	public function testDownloadHelperSupportsExpectedFileFormats(){
		$hasArray = ['csv', 'xls', 'xlsx'];

		foreach ($hasArray as $ext){
			$this->assertContains($ext, DownloadHelper::VALID_EXPORT_EXTENSIONS);
		}
	}
}