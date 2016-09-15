<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Monster;

class MonsterController extends Controller
{
	const CONTROLLER_NAME = "monster";

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
		$monster = new Monster();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView("edit"), compact('monster', 'headers'));
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
		Monster::create($request->all());
		return redirect()->action($this->getControllerAction('index'), self::sendRecordAddedSuccessfully());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Monster $monster)
	{
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(self::SHOW), compact('monster', 'headers'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Monster $monster)
	{
		$headers = $this->getUpdateHeaders($monster->id);
		return view($this->getControllerView("edit"), compact('monster', 'headers'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Monster $monster)
	{
		$monster -> update($request->all());
		return redirect()->action($this->getShowControllerAction(), self::addUpdateSuccessMessage(compact('monster')));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Monster $monster)
	{
		$monster->delete();
	}
}
