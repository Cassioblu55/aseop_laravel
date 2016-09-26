<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use App\NonPlayerCharacter;
use App\Http\Requests;

class NpcController extends Controller
{

	const CONTROLLER_NAME = "npc";

	private $logging;

	public function __construct(){
		$this->setControllerNames(self::CONTROLLER_NAME);

		$this->logging = new Logging(self::class);

		$this->middleware('auth', ['except' => ['show']]);

		parent::__construct(self::class);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$npc = new NonPlayerCharacter();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView(Messages::EDIT), compact('npc', 'headers'));
	}

	public function generate(){
		$npc = NonPlayerCharacter::generate();
		return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact("npc")));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$npc = new NonPlayerCharacter($request->all());
		$npc->setRequiredMissing();
		return $this->validateStore($npc, false, 'npc');
	}

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = NonPlayerCharacter::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(NonPlayerCharacter $npc)
	{
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(Messages::SHOW), compact('npc', 'headers'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(NonPlayerCharacter $npc)
	{
		$headers = $this->getUpdateHeaders($npc->id);
		return view($this->getControllerView("edit"), compact('npc', 'headers'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, NonPlayerCharacter $npc)
	{
		return $this->validateUpdate($request, $npc, false, 'npc');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(NonPlayerCharacter $npc)
	{
		$npc->delete();
		return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
	}
}
