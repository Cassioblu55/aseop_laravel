<?php
/**
 * Created by PhpStorm.
 * User: cbhudson
 * Date: 10/6/16
 * Time: 11:43 AM
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;

class DungeonTraitTest extends TestCase
{

	use DatabaseTransactions;

	private $logging;

	public function __construct()
	{
		$this->logging = new \App\Services\Logging(self::class);
		parent::__construct();
	}

}