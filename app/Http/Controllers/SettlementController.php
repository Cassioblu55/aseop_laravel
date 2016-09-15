<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Settlement;

class SettlementController extends Controller
{
	const CONTROLLER_NAME = "settlement";

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
		$settlement = new Settlement();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView("edit"), compact('settlement', 'headers'));
	}


	public function generate(){
		$settlement = Settlement::generate();
		return redirect()->action($this->getControllerAction('edit'), [$settlement]);
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
		$settlement = Settlement::create($request->all());
		return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact('settlement')));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Settlement $settlement)
	{
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(self::SHOW), compact('settlement', 'headers'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Settlement $settlement)
	{
		$headers = $this->getUpdateHeaders($settlement->id);
		return view($this->getControllerView("edit"), compact('settlement', 'headers'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Settlement $settlement)
	{
		$settlement -> update($request->all());
		return redirect()->action($this->getShowControllerAction(), self::addUpdateSuccessMessage(compact('settlement')));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Settlement $settlement)
	{
		$settlement->delete();
	}
}
