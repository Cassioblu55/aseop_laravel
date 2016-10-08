<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use Illuminate\Foundation\Testing\DatabaseTransactions;

class Base_name extends TestCase
{
    use DatabaseTransactions;

    private $logging;

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }

}