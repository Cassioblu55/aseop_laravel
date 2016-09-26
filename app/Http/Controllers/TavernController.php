<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use App\TavernTrait;
use Illuminate\Http\Request;
use App\Tavern;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;

class TavernController extends Controller
{
	const CONTROLLER_NAME = "tavern";

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
		$tavern = new Tavern();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView(Messages::EDIT), compact('tavern', 'headers'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$tavern = new Tavern($request->all());
		$tavern->setRequiredMissing();
		return $this->validateStore($tavern);
	}

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = Tavern::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
	}

	public function generate(){
		$tavern = Tavern::generate();
		return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact("tavern")));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Tavern $tavern)
	{
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(Messages::SHOW), compact('tavern', 'headers'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Tavern $tavern)
	{
		$headers = $this->getUpdateHeaders($tavern->id);
		return view($this->getControllerView(Messages::EDIT), compact('tavern', 'headers'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Tavern $tavern)
	{
		return $this->validateUpdate($request, $tavern);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Tavern $tavern)
	{
		$tavern->delete();
		return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
	}
}
