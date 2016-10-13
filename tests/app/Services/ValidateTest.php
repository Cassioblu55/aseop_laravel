<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Services\Validate;
use Illuminate\Http\Request;

class ValidateTest extends TestCase
{

    private $logging;

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }

    public function testGetUniqueWithIgnoreSelfRuleShouldReturnIgnoreSelfRule()
    {
	    $rule = Validate::getUniqueWithIgnoreSelfRule("foo", "5", "id", "required");

	    $this->assertEquals("unique:foo,id,5|required", $rule);
    }

	public function testGetUniqueWithIgnoreSelfRuleShouldReturnIgnoreSelfRuleWithinNoColumnNameOrAdditionalValidation()
	{
		$rule = Validate::getUniqueWithIgnoreSelfRule("foo", "4");

		$this->assertEquals("unique:foo,id,4", $rule);
	}

	public function testGetInArrayRuleShouldReturnGetArrayRule(){
		$array = ["foo", "bar", "bars"];

		$rule = Validate::getInArrayRule($array);

		$this->assertEquals("in:foo,bar,bars", $rule);
	}

	public function testGetInArrayRuleShouldReturnGetArrayRuleWithAdditionalValidation(){
		$array = ["foo", "bar", "bars"];

		$rule = Validate::getInArrayRule($array, "required");

		$this->assertEquals("in:foo,bar,bars|required", $rule);
	}

	public function testGetErrorMessageShouldSayNoErrorsFoundIfNoneExist()
	{
		$this->assertEquals("No errors present", Validate::getErrorMessage(new Request(), []));
	}

	public function testGetErrorMessageShouldGiveErrorMessageOnFailedValidation()
	{
		$rules = ["name" => "required",];
		$request = new Request();

		$expectedErrorMessage = 'Could not save: {"name":["The name field is required."]}';

		$this->assertEquals($expectedErrorMessage, Validate::getErrorMessage($request, $rules));
	}

	public function testGetErrorMessageShouldSayErrrosPresent()
	{
		$rules = ["name" => "required"];
		$request = new Request();

		$this->assertFalse(Validate::validUpdateData($request, $rules));

		$this->assertTrue(Validate::validUpdateData($request, []));
	}

	public function testValidUpdateDataFromGenericModelShouldReturnFalseWhenErrorsFoundInGenericModel(){
		$this->assertFalse(Validate::validUpdateDataFromGenericModel(new Request(), new \App\Dungeon()));
	}

	public function testValidRollReturnsTrueIfValidRollAndFalseIfInvaildRoll(){
		$validRoll = "1d6+4";
		$this->assertTrue(Validate::validRoll($validRoll));

		$validRoll = "1D6+4";
		$this->assertTrue(Validate::validRoll($validRoll));

		$validRoll = "10d6+0";
		$this->assertTrue(Validate::validRoll($validRoll));

		$validRoll = "100d760+4";
		$this->assertTrue(Validate::validRoll($validRoll));

		$validRoll = "100d200-704";
		$this->assertTrue(Validate::validRoll($validRoll));

		$invalidRoll = "this is an invalid roll";
		$this->assertFalse(Validate::validRoll($invalidRoll));

		$invalidRoll = "3d*5";
		$this->assertFalse(Validate::validRoll($invalidRoll));

		$invalidRoll = "5e+5";
		$this->assertFalse(Validate::validRoll($invalidRoll));

		$invalidRoll = "3d-y";
		$this->assertFalse(Validate::validRoll($invalidRoll));
	}

	public function testValidRollStingWillReturnTrueOnValidRollString(){
		$validRollString = "1d4+5";
		$this->assertTrue(Validate::validRollString($validRollString));

		$validRollString = "100d760+4,1D6+4,10d6+4,100d200-704";
		$this->assertTrue(Validate::validRollString($validRollString));

		$invalidRollString = "100d760+4,1D6+4,10d6+4,100d200*704";
		$this->assertFalse(Validate::validRollString($invalidRollString));
	}

	public function testValidRollStringReturnsFalseOnEmptyString(){
		$this->assertFalse(Validate::validRollString(""));
	}

	public function testBlankOrNullShouldReturnTrueIfStringIsBlackrNull(){
		$this->assertTrue(Validate::blackOrNull(""));
		$this->assertTrue((Validate::blackOrNull(null)));
		$this->assertFalse(Validate::blackOrNull("foo bar"));
	}

	public function testAllInArrayTrueWillReturnTrueIfAllInArrayTrueOrOne(){
		$validArray = [true, 1, true, 1];
		$this->assertTrue(Validate::allInArrayTrue($validArray));


		$invalidArray = [false];
		$this->assertFalse(Validate::allInArrayTrue($invalidArray));

		$invalidArray = [0];
		$this->assertFalse(Validate::allInArrayTrue($invalidArray));
	}

	public function testStringOfJsonArrayContainsKeysShouldFailIfJsonArrayAsStringDoesNotContainKeys()
	{
		$requiredKeys = ['name', 'description'];

		$validJsonArrays = ['', null, '[{"name":"foo","description":"bar"}]', "[]"];
		foreach ($validJsonArrays as $row) {
			$this->assertTrue(Validate::stringOfJsonArrayContainsKeys($row, $requiredKeys, true));
		}

		$invalidJsonArrayNotJsonArrayString = "foo bar";
		$this->assertFalse(Validate::stringOfJsonArrayContainsKeys($invalidJsonArrayNotJsonArrayString, $requiredKeys, true));

		$invalidJsonArrayNotArray = '{"name":"foo","description":"bar"}';
		$this->assertFalse(
			Validate::stringOfJsonArrayContainsKeys($invalidJsonArrayNotArray, $requiredKeys));

		$invalidJsonArrayMissingKey = '[{"name":"foo"}]';
		$this->assertFalse(Validate::stringOfJsonArrayContainsKeys($invalidJsonArrayMissingKey, $requiredKeys));

		$invalidJsonBlack = '';
		$this->assertFalse(Validate::stringOfJsonArrayContainsKeys($invalidJsonBlack, $requiredKeys));

		$invalidJsonNull = null;
		$this->assertFalse(Validate::stringOfJsonArrayContainsKeys($invalidJsonNull, $requiredKeys));
	}

}