<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\DungeonTrait;

class DungeonTraitController extends Controller
{

	const CONTROLLER_NAME = "dungeonTrait";

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
		$dungeonTrait = new DungeonTrait();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView(Messages::EDIT), compact('dungeonTrait', 'headers'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$dungeonTrait = new DungeonTrait($request->all());
		$dungeonTrait->setRequiredMissing();
		return $this->validateAndRedirect($dungeonTrait, true);
	}

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = DungeonTrait::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(DungeonTrait $dungeonTrait)
	{
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(Messages::SHOW), compact('dungeonTrait', 'headers'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(DungeonTrait $dungeonTrait)
	{
		$headers = $this->getUpdateHeaders($dungeonTrait->id);
		return view($this->getControllerView(Messages::EDIT), compact('dungeonTrait', 'headers'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, DungeonTrait $dungeonTrait)
	{
		$dungeonTrait -> update($request->all());
		return $this->validateAndRedirect($dungeonTrait, true);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(DungeonTrait $dungeonTrait)
	{
		$dungeonTrait->delete();
		return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
	}
}
