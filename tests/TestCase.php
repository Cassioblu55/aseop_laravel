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
}
