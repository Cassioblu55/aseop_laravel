<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tavern;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;

class TavernController extends Controller
{
	const CONTROLLER_NAME = "tavern";

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
		$tavern = new Tavern();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView(self::EDIT), compact('tavern', 'headers'));
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
		$tavern = Tavern::create($request->all());
		return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact("tavern")));
	}

	public function generate(){
		$tavern = Tavern::generate();
		return redirect()->action($this->getEditControllerAction(), [$tavern]);
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
		return view($this->getControllerView('show'), compact('tavern', 'headers'));
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
		return view($this->getControllerView(self::EDIT), compact('tavern', 'headers'));
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
		$tavern -> update($request->all());
		return redirect()->action($this->getShowControllerAction(), self::addUpdateSuccessMessage(compact('tavern')));
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
	}
}
