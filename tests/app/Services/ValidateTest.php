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

}