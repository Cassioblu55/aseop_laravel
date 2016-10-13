<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

//use $usePath

class Base_name extends TestCase
{
    private $logging;
    //private $user;

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);
        parent::__construct();
    }

    public function setUp(){
        parent::setUp();

        //$this->user = factory(\App\User::class)->create();
        //$this->actingAs($this->user);

    }

    public function tearDown()
    {
        //$this->actingAs(new \App\User());
        parent::tearDown();
    }

}