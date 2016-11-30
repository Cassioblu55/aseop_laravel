<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\Controller;
use App\Http\Controllers\DungeonController;

class AbstractControllerTest extends TestCase
{
    private $logging;
	private $controller;
    //private $user;

    public function __construct()
    {
        $this->logging = new \App\Services\Logging(self::class);


        parent::__construct();
    }

    public function setUp(){
        parent::setUp();

	    $this->assertEquals("dungeon", DungeonController::CONTROLLER_NAME);
	    $this->controller = new DungeonController();

        //$this->user = factory(\App\User::class)->create();
        //$this->actingAs($this->user);

    }

    public function tearDown()
    {
        //$this->actingAs(new \App\User());
	    $this->controller = null;
	    parent::tearDown();
    }

    public function testSetControllerNameSpaceShouldSetControllerNames(){
	    $this->assertEquals('dungeons', $this->controller->getControllerNameSpace());
	    $this->assertEquals('DungeonController', $this->controller->getControllerProperName());
	    $this->assertEquals('dungeon', $this->controller->getControllerViewPrefix());
	    $this->assertEquals('dungeon', $this->controller->getControllerModelName());
	    $this->assertEquals('dungeons', $this->controller->getTableName());
    }

    public function testGetControllerActionShouldReturnCorrectControllerActionName()
    {
	    $this->assertEquals("DungeonController@foo", $this->controller->getControllerAction('foo'));
    }

    public function testGetControllerViewShouldReturnCorrectViewName(){
	    $this->assertEquals('dungeons.dungeon_foo', $this->controller->getControllerView('foo'));
    }

    public function testGetCreateHeadersShouldReturnCreateHeaders(){
	    $createHeaders = $this->controller->getCreateHeaders();

	    $this->assertNotNull($createHeaders);
	    $this->assertEquals('Create', $createHeaders->createOrUpdate);
	    $this->assertEquals(url('dungeons'), $createHeaders->postLocation);
	    $this->assertEquals('POST', $createHeaders->methodField);
	    $this->assertEquals('Add', $createHeaders->addOrSave);

	    $additionalData = $createHeaders->dataDefaults;
	    $this->assertNotNull($additionalData);
	    $this->assertEquals('dungeon', $additionalData->model);
	    $this->assertEquals('DungeonAddEditController', $additionalData->addEditController);
	    $this->assertEquals('DungeonIndexController', $additionalData->indexController);
	    $this->assertEquals('DungeonShowController', $additionalData->showController);
	    $this->assertEquals('DungeonUploadController', $additionalData->uploadController);
    }

	public function testGetUpdateHeadersShouldReturnUpdateHeaders(){
		$createHeaders = $this->controller->getUpdateHeaders(1);

		$this->assertNotNull($createHeaders);
		$this->assertEquals('Update', $createHeaders->createOrUpdate);
		$this->assertEquals(url('dungeons')."/1", $createHeaders->postLocation);
		$this->assertEquals('PATCH', $createHeaders->methodField);
		$this->assertEquals('Save', $createHeaders->addOrSave);

		$additionalData = $createHeaders->dataDefaults;
		$this->assertNotNull($additionalData);
		$this->assertEquals('dungeon', $additionalData->model);
		$this->assertEquals('DungeonAddEditController', $additionalData->addEditController);
		$this->assertEquals('DungeonIndexController', $additionalData->indexController);
		$this->assertEquals('DungeonShowController', $additionalData->showController);
		$this->assertEquals('DungeonUploadController', $additionalData->uploadController);
	}

	public function testGetIndexHeadersShouldReturnCorrectIndexHeaders(){
		$indexHeaders = $this->controller->getIndexHeaders();

		$additionalData = $indexHeaders->dataDefaults;
		$this->assertNotNull($additionalData);
		$this->assertEquals('dungeon', $additionalData->model);
		$this->assertEquals('DungeonAddEditController', $additionalData->addEditController);
		$this->assertEquals('DungeonIndexController', $additionalData->indexController);
		$this->assertEquals('DungeonShowController', $additionalData->showController);
		$this->assertEquals('DungeonUploadController', $additionalData->uploadController);
	}

	public function testGetShowHeadersShouldReturnCorrectShowHeaders(){
		$showHeaders = $this->controller->getShowHeaders();

		$additionalData = $showHeaders->dataDefaults;
		$this->assertNotNull($additionalData);
		$this->assertEquals('dungeon', $additionalData->model);
		$this->assertEquals('DungeonAddEditController', $additionalData->addEditController);
		$this->assertEquals('DungeonIndexController', $additionalData->indexController);
		$this->assertEquals('DungeonShowController', $additionalData->showController);
		$this->assertEquals('DungeonUploadController', $additionalData->uploadController);
	}

	public function testGetUploadHeadersShouldReturnCorrectUploadHeaders(){
		$uploadHeaders = $this->controller->getUploadHeaders();

		$this->assertEquals(url('dungeons')."/upload", $uploadHeaders->postLocation);
		$this->assertEquals('Upload', $uploadHeaders->addOrSave);
		$this->assertEquals("POST", $uploadHeaders->methodField);

		$additionalData = $uploadHeaders->dataDefaults;
		$this->assertNotNull($additionalData);
		$this->assertEquals('dungeon', $additionalData->model);
		$this->assertEquals('DungeonAddEditController', $additionalData->addEditController);
		$this->assertEquals('DungeonIndexController', $additionalData->indexController);
		$this->assertEquals('DungeonShowController', $additionalData->showController);
		$this->assertEquals('DungeonUploadController', $additionalData->uploadController);
	}



}