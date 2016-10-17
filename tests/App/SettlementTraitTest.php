<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\SettlementTrait;
use App\TestingUtils\FileTesting;

class SettlementTraitTest extends TestCase
{
    private $logging;
    private $user;

	const TEST_UPLOAD_ROW = [
		"trait" => "Harmony in all place",
		"type" => "race_relations"
	];

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }

    public function setUp(){
        parent::setUp();

        $this->user = factory(\App\User::class)->create();
        $this->actingAs($this->user);

    }

    public function tearDown()
    {
        $this->actingAs(new \App\User());
        parent::tearDown();
    }

	public function testValidateShouldFailIfTraitNullOrBlank(){
		$settlementTrait = factory(SettlementTrait::class)->make();
		$this->assertTrue($settlementTrait->validate());

		$settlementTrait->trait = '';
		$this->assertFalse($settlementTrait->validate());

		$expectedError = 'Could not save: {"trait":["The trait field is required."]}';
		$this->assertEquals($expectedError, $settlementTrait->getErrorMessage());

		$settlementTrait->trait = null;
		$this->assertFalse($settlementTrait->validate());

		$expectedError = 'Could not save: {"trait":["The trait field is required."]}';
		$this->assertEquals($expectedError, $settlementTrait->getErrorMessage());
	}

	public function testValidateShouldFailIfTypeNullOrBlank(){
		$settlementTrait = factory(SettlementTrait::class)->make();
		$this->assertTrue($settlementTrait->validate());

		$settlementTrait->type = '';
		$this->assertFalse($settlementTrait->validate());

		$expectedError = 'Could not save: {"type":["The type field is required."]}';
		$this->assertEquals($expectedError, $settlementTrait->getErrorMessage());

		$settlementTrait->type = null;
		$this->assertFalse($settlementTrait->validate());

		$expectedError = 'Could not save: {"type":["The selected type is invalid.","The type field is required."]}';
		$this->assertEquals($expectedError, $settlementTrait->getErrorMessage());

		$settlementTrait->type = 'foo';
		$this->assertFalse($settlementTrait->validate());

		$expectedError = 'Could not save: {"type":["The selected type is invalid."]}';
		$this->assertEquals($expectedError, $settlementTrait->getErrorMessage());
	}

	public function testDuplicateVillianTraitShouldNotBeValid(){
		$settlementTrait = factory(SettlementTrait::class)->create();
		$this->assertTrue($settlementTrait->validate());

		$settlementTraitTwo = factory(SettlementTrait::class)->make();
		$this->assertFalse($settlementTraitTwo->validate());

		$expectedError = 'Could not save: {"duplication_error":["Duplicate object found."]}';
		$this->assertEquals($expectedError, $settlementTraitTwo->getErrorMessage());
	}

	public function testUploadShouldAddSettlementTrait(){

		$path = "resources/assets/testing/csv/SettlementTrait/testUpload_DO_NOT_EDIT.csv";
		new FileTesting($path);

		SettlementTrait::truncate();

		$count = count(SettlementTrait::all());

		$message = SettlementTrait::upload($path);

		$this->assertEquals($count+1, count(SettlementTrait::all()));

		$this->assertEquals("1 records added 0 updated 0 records could not be uploaded", $message);

		$settlementTrait = SettlementTrait::where("trait", "Harmony in all place")->first();

		$this->assertNotNull($settlementTrait);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $settlementTrait->toArray());
	}

	public function testSetUploadValuesShouldAddValueToObjectFromRow(){
		$settlementTrait = new SettlementTrait();

		$settlementTrait->setUploadValues(self::TEST_UPLOAD_ROW);

		$this->assertHashesHaveEqualValues(self::TEST_UPLOAD_ROW, $settlementTrait->toArray());
	}
}