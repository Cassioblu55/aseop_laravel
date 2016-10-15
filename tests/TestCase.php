<?php


abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		putenv('DB_DEFAULT=sqlite_testing');

		global $app;

		if (is_null($app)) {
			$app = require __DIR__.'/../bootstrap/app.php';

			$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
		}

		return $app;

	}

	public function setUp()
	{
		parent::setUp();
		Artisan::call('migrate');
	}

	public function tearDown()
	{
		Artisan::call('migrate:reset');

		\Mockery::close();
		//parent::tearDown();
	}

	protected function assertHashesHaveEqualValues($expectedValues, $actualValues){
		foreach ($expectedValues as $key => $value){
			$this->assertArrayHasKey($key,$actualValues);
			$this->assertEquals($value, $actualValues[$key]);
		}
	}

	public static function ensureNpcOfIdOneExists(){
		$npc = \App\NonPlayerCharacter::where("id", 1)->first();
		if($npc == null){
			$npc = factory(\App\NonPlayerCharacter::class)->create();
			if($npc->id != 1){
				$npc->id = 1;
				$npc->runUpdateOrSave();
			}
		}
	}

	public static function ensureTrapOfIdOneExists(){
		$trap = \App\Trap::where("id", 1)->first();
		if($trap == null){
			$trap = factory(\App\Trap::class)->create();
			if($trap->id != 1){
				$trap->id = 1;
				$trap->runUpdateOrSave();
			}
		}
	}

}
