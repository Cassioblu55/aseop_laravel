<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\TestingUtils\FileTesting;
use App\Services\CSVParser;

class CSVParserTest extends TestCase
{
    use DatabaseTransactions;

    private $logging;

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }

    public function testGetCSVDataShouldReturnDataFromCSVFileGivenFilePath(){
	$path  = "resources/assets/testing/csv/CSVParser/validData_DO_NOT_EDIT.csv";
	self::assertFileExists($path);

	$parser = new CSVParser($path, ["name"]);
	$data = $parser->getCSVData();

	$this->assertEquals(1, count($data));

	$row = $data[0];
	$this->assertArrayHasKey("name", $row);
	$this->assertEquals("foo", $row['name']);
	}

	public function testGetCSVDataShouldNotReturnUnwantedData(){
		$path  = "resources/assets/testing/csv/CSVParser/validData_DO_NOT_EDIT.csv";
		self::assertFileExists($path);

		$parser = new CSVParser($path, []);
		$data = $parser->getCSVData();

		$this->assertEquals(1, count($data));

		$row = $data[0];
		$this->assertArrayNotHasKey("name", $row);
	}

	public function testGetCSVDataOrderShouldNotMatterForGivenColumns(){
		$path  = "resources/assets/testing/csv/CSVParser/multiColumns_DO_NOT_EDIT.csv";
		self::assertFileExists($path);

		$parser = new CSVParser($path, ["name", "bar"]);
		$data = $parser->getCSVData();

		$this->assertEquals(1, count($data));

		$row = $data[0];
		$this->assertArrayHasKey("name", $row);
		$this->assertEquals("foo", $row['name']);

		$this->assertArrayHasKey("bar", $row);
		$this->assertEquals("test", $row['bar']);

		$parser = new CSVParser($path, ["bar", "name"]);
		$data = $parser->getCSVData();

		$this->assertEquals(1, count($data));

		$row = $data[0];
		$this->assertArrayHasKey("name", $row);
		$this->assertEquals("foo", $row['name']);

		$this->assertArrayHasKey("bar", $row);
		$this->assertEquals("test", $row['bar']);
	}

	public function testGetCSVDataShouldReturnEmptyArrayIfGivenFilePathDoesNotExist(){
		$path  = "resources/assets/testing/csv/CSVParser/this_file_does_not_exist_DO_NOT_EDIT.csv";
		self::assertFileNotExists($path);

		$parser = new CSVParser($path, ["name", "bar"]);
		$data = $parser->getCSVData();

		$this->assertEquals(0, count($data));
	}
}