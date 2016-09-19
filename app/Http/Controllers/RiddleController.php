<?php

namespace App\Http\Controllers;

use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Riddle;
use App\Services\GetRandom;

class RiddleController extends Controller
{
	const CONTROLLER_NAME = "riddle";

	public function __construct(){
		$this->setControllerNames(self::CONTROLLER_NAME);

		$this->middleware('auth', ['except' => ['show']]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$riddle = new Riddle();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView(Messages::EDIT), compact('riddle', 'headers'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$request['owner_id'] = Auth::user()->id;
		$request['approved'] = false;
		$riddle = Riddle::create($request->all());
		return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact("riddle")));
	}

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = Riddle::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Riddle $riddle)
	{
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(Messages::SHOW), compact('riddle', 'headers'));
	}

	public function random(){
		return redirect()->action($this->getShowControllerAction(), [Riddle::random()]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Riddle $riddle)
	{
		$headers = $this->getUpdateHeaders($riddle->id);
		return view($this->getControllerView(Messages::EDIT), compact('riddle', 'headers'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Riddle $riddle)
	{
		$riddle -> update($request->all());
		return redirect()->action($this->getShowControllerAction(), self::addUpdateSuccessMessage(compact('riddle')));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Riddle $riddle)
	{
		$riddle->delete();
		return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
	}
}
