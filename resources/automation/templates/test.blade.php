<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */


class Base_name extends TestCase
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

}