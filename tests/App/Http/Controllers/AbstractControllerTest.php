<?php
/**
 * Created by make:aesopTest command using template: test.blade.php
 */

use App\Http\Controllers\AbstractController;
use App\Http\Controllers\DungeonController;
use Illuminate\Http\Request;

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

	public function testGetPostHeadersShouldReturnCOrrectPostHeaders(){
		$postHeaders = $this->controller->getPostHeaders();

		$this->assertEquals(url("dungeons"), $postHeaders->postLocation);
		$this->assertEquals("POST", $postHeaders->methodField);
	}

	public function testSendRecordUpdatedSuccessfullyShouldCreateAUpdateSuccessMessage(){
		$message = $this->controller->sendRecordUpdatedSuccessfully();

		$this->assertArrayHasKey("successMessage", $message);
		$this->assertEquals("Record Updated Successfully", $message['successMessage']);

		$altMessage = $this->controller->sendRecordUpdatedSuccessfully("The Update was Successful");
		$this->assertArrayHasKey("successMessage", $altMessage);
		$this->assertEquals("The Update was Successful", $altMessage['successMessage']);
	}

	public function testSendRecordAddedSuccessfullyShouldCreateAddedSuccessMessage(){
		$message = $this->controller->sendRecordAddedSuccessfully();

		$this->assertArrayHasKey("successMessage", $message);
		$this->assertEquals("Record Added Successfully", $message['successMessage']);

		$altMessage = $this->controller->sendRecordAddedSuccessfully("New Record Created Successfully");
		$this->assertArrayHasKey("successMessage", $altMessage);
		$this->assertEquals("New Record Created Successfully", $altMessage['successMessage']);
	}

	public function testSendSuccessfullyDeletedMesageShouldCreateDeletedSuccessMessage(){
		$message = $this->controller->sendSuccessfullyDeletedMessage();

		$this->assertArrayHasKey("successMessage", $message);
		$this->assertEquals("Record Deleted Successfully", $message['successMessage']);

		$altMessage = $this->controller->sendSuccessfullyDeletedMessage("Record Deleted");
		$this->assertArrayHasKey("successMessage", $altMessage);
		$this->assertEquals("Record Deleted", $altMessage['successMessage']);
	}

	public function testControllerActionsShouldShouldReturnCorrectControllerActionNames(){
		$this->assertEquals("DungeonController@index", $this->controller->getIndexControllerAction());
		$this->assertEquals("DungeonController@create", $this->controller->getCreateControllerAction());
		$this->assertEquals("DungeonController@edit", $this->controller->getEditControllerAction());
		$this->assertEquals("DungeonController@show", $this->controller->getShowControllerAction());
	}

	public function testAddMessagesShouldAddArrayOfUrlParamsToDataHash(){
		$data = ["messageOne" => "This message was already here"];

		$urlPrams = [
			"messageTwo" => "This is message two"
		];

		$newData = DungeonController::addMessages($data, $urlPrams);

		$this->assertArrayHasKey("messageOne", $newData);
		$this->assertEquals("This message was already here", $newData['messageOne']);

		$this->assertArrayHasKey("messageTwo", $newData);
		$this->assertEquals("This is message two", $newData['messageTwo']);
	}

	public function testAddMessagesShouldOverrideExistingMessages(){
		$data = ["messageOne" => "This message was already here"];

		$urlPrams = [
			"messageOne" => "This is the new message"
			];

		$newData = DungeonController::addMessages($data, $urlPrams);
		$this->assertArrayHasKey("messageOne", $newData);
		$this->assertEquals("This is the new message", $newData['messageOne']);
	}

	public function testAddUpdatedFailedMessageShouldAddUpdateFailedMessage(){
		$data = ["messageOne" => "This message was already here"];
		$newData = DungeonController::addUpdatedFailedMessage($data);

		$this->assertArrayHasKey("messageOne", $newData);
		$this->assertEquals("This message was already here", $newData['messageOne']);

		$this->assertArrayHasKey("errorMessage", $newData);
		$this->assertEquals("Record failed to update", $newData['errorMessage']);
	}

	public function testAddUpdateSuccessMessagShouldAddUpdateSuccessMessage(){
		$data = ["messageOne" => "This message was already here"];
		$newData = DungeonController::addUpdateSuccessMessage($data);

		$this->assertArrayHasKey("messageOne", $newData);
		$this->assertEquals("This message was already here", $newData['messageOne']);

		$this->assertArrayHasKey("successMessage", $newData);
		$this->assertEquals("Record Updated Successfully", $newData['successMessage']);
	}

	public function testAddAddedSuccessMessageShouldAddAddedSuccessMessage(){
		$data = ["messageOne" => "This message was already here"];
		$newData = DungeonController::addAddedSuccessMessage($data);

		$this->assertArrayHasKey("messageOne", $newData);
		$this->assertEquals("This message was already here", $newData['messageOne']);

		$this->assertArrayHasKey("successMessage", $newData);
		$this->assertEquals("Record Added Successfully", $newData['successMessage']);
	}

	public function testAddAddedFailedMessageShouldAddAddedFailedMessage(){
		$data = ["messageOne" => "This message was already here"];
		$newData = DungeonController::addAddedFailedMessage($data);

		$this->assertArrayHasKey("messageOne", $newData);
		$this->assertEquals("This message was already here", $newData['messageOne']);

		$this->assertArrayHasKey("errorMessage", $newData);
		$this->assertEquals("Record could not be added", $newData['errorMessage']);
	}

	public function testValidateStoreShouldOnlyCreateNewObjectWhenObjectIsVaild(){
		$user = factory(\App\User::class)->create();
		$this->actingAs($user);

		factory(\App\Trap::class)->create();

		$dungeon = factory(App\Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());


		$view = $this->controller->validateStore($dungeon);

		$this->assertNotNull($dungeon->id);

		$this->assertEquals(url('dungeons/1?successMessage=Record+Added+Successfully'), $view->getTargetUrl());
	}

	public function testValidateStoreShouldRedirectToIndexWhenToldToAndObjectIsVaild(){
		$user = factory(\App\User::class)->create();
		$this->actingAs($user);

		factory(\App\Trap::class)->create();

		$dungeon = factory(App\Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());


		$view = $this->controller->validateStore($dungeon, true);
		$this->assertNotNull($dungeon->id);

		$this->assertEquals(url('dungeons?successMessage=Record+Added+Successfully'), $view->getTargetUrl());
	}

	public function testValidateStoreShouldNotSaveWhenObjectIsInvaild(){
		$user = factory(\App\User::class)->create();
		$this->actingAs($user);

		factory(\App\Trap::class)->create();

		$dungeon = factory(App\Dungeon::class)->make();
		$this->assertTrue($dungeon->validate());

		$dungeon->name = null;

		$this->assertFalse($dungeon->validate());

		$view = $this->controller->validateStore($dungeon);
		$this->assertNull($dungeon->id);

		$this->assertEquals(url('dungeons/create?errorMessage=Record+could+not+be+added'), $view->getTargetUrl());

		$view = $this->controller->validateStore($dungeon, true);
		$this->assertNull($dungeon->id);

		$this->assertEquals(url('dungeons/create?errorMessage=Record+could+not+be+added'), $view->getTargetUrl());
	}


	public function testValidateUpdateShouldOnlyCreateNewObjectWhenObjectIsVaild(){
		$user = factory(\App\User::class)->create();
		$this->actingAs($user);

		factory(\App\Trap::class)->create();

		$dungeon = factory(App\Dungeon::class)->create();
		$this->assertTrue($dungeon->validate());

		$this->assertNotNull($dungeon->id);
		$this->assertEquals("foo", $dungeon->name);

		$newDungeon = $dungeon;
		$newDungeon->name = "This is a test";
		$request = 	new Request([], $newDungeon->toArray());

		$view = $this->controller->validateUpdate($request,$dungeon);
		$savedDungeon = \App\Dungeon::findById($dungeon->id);
		$this->assertEquals("This is a test", $savedDungeon->name);

		$this->assertEquals(url('dungeons/1?successMessage=Record+Updated+Successfully'), $view->getTargetUrl());
	}

	public function testValidateUpdateShouldRedirectToIndexWhenToldToAndObjectIsVaild(){
		$user = factory(\App\User::class)->create();
		$this->actingAs($user);

		factory(\App\Trap::class)->create();

		$dungeon = factory(App\Dungeon::class)->create();
		$this->assertTrue($dungeon->validate());

		$this->assertNotNull($dungeon->id);
		$this->assertEquals("foo", $dungeon->name);

		$newDungeon = $dungeon;
		$newDungeon->name = "This is a test";
		$request = 	new Request([], $newDungeon->toArray());

		$view = $this->controller->validateUpdate($request,$dungeon, true);

		$savedDungeon = \App\Dungeon::findById($dungeon->id);
		$this->assertEquals("This is a test", $savedDungeon->name);

		$this->assertEquals(url('dungeons?successMessage=Record+Updated+Successfully'), $view->getTargetUrl());
	}

	public function testValidateUpdateShouldNotSaveWhenObjectIsInvaild(){
		$user = factory(\App\User::class)->create();
		$this->actingAs($user);

		factory(\App\Trap::class)->create();

		$dungeon = factory(App\Dungeon::class)->create();
		$this->assertTrue($dungeon->validate());

		$this->assertNotNull($dungeon->id);

		$this->assertEquals("foo", $dungeon->name);

		$newDungeon = $dungeon;
		$newDungeon->name = null;
		$this->assertFalse($newDungeon->validate());
		$request = 	new Request([], $newDungeon->toArray());

		$view = $this->controller->validateUpdate($request,$dungeon, true);
		$this->assertNotNull($dungeon->id);

		$savedDungeon = \App\Dungeon::findById($dungeon->id);
		$this->assertEquals("foo", $savedDungeon->name);

		$this->assertEquals(url('dungeons/1/edit?errorMessage=Record+failed+to+update'), $view->getTargetUrl());

		$view = $this->controller->validateUpdate(new \Illuminate\Http\Request($dungeon->toArray()), $dungeon, true);
		$this->assertNotNull($dungeon->id);

		$savedDungeon = \App\Dungeon::findById($dungeon->id);
		$this->assertEquals("foo", $savedDungeon->name);

		$this->assertEquals(url('dungeons/1/edit?errorMessage=Record+failed+to+update'), $view->getTargetUrl());
	}


	public function testIndexShouldRetrunIndexViewPage(){
		$view = $this->controller->index();

		$this->assertEquals("dungeons.dungeon_index", $view->name());

		$this->assertTrue($view->__isset("headers"));

		$headers = $view->getData()['headers'];
		$this->assertObjectHasAttribute("dataDefaults", $headers);

		$dataDefaults = $headers->dataDefaults;

		$this->assertEquals('dungeon', $dataDefaults->model);
		$this->assertEquals('DungeonAddEditController', $dataDefaults->addEditController);
		$this->assertEquals('DungeonIndexController', $dataDefaults->indexController);
		$this->assertEquals('DungeonShowController', $dataDefaults->showController);
		$this->assertEquals('DungeonUploadController', $dataDefaults->uploadController);
	}

}