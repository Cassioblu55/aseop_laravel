<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\NonPlayerCharacterTrait;

class NonPlayerCharacterTraitController extends Controller
{
	const CONTROLLER_NAME = "npcTrait";

	private $logging;

	public function __construct(){
		$this->setControllerNames(self::CONTROLLER_NAME);
		$this->setControllerProperName("NonPlayerCharacterTraitController");

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
		$npcTrait = new NonPlayerCharacterTrait();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView(Messages::EDIT), compact('npcTrait', 'headers'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$npcTrait = new NonPlayerCharacterTrait($request->all());
		$npcTrait->setRequiredMissing();
		return $this->validateStore($npcTrait, true, 'npcTrait');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(NonPlayerCharacterTrait $npcTrait)
	{
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(Messages::SHOW), compact('npcTrait', 'headers'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(NonPlayerCharacterTrait $npcTrait)
	{
		$headers = $this->getUpdateHeaders($npcTrait->id);
		return view($this->getControllerView(Messages::EDIT), compact('npcTrait', 'headers'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, NonPlayerCharacterTrait $npcTrait)
	{
		return $this->validateUpdate($request, $npcTrait, true, 'npcTrait');
	}


	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = NonPlayerCharacterTrait::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(NonPlayerCharacterTrait $npcTrait)
	{
		$npcTrait->delete();
		return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
	}
}
