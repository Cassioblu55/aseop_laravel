<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\DungeonTrait;

class DungeonTraitController extends Controller
{

	const CONTROLLER_NAME = "dungeonTrait";

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
		$dungeonTrait = new DungeonTrait();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView(self::EDIT), compact('dungeonTrait', 'headers'));
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
		DungeonTrait::create($request->all());
		return redirect()->action($this->getControllerAction('index'), self::sendRecordAddedSuccessfully());
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
		return view($this->getControllerView(self::SHOW), compact('dungeonTrait', 'headers'));
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
		return view($this->getControllerView(self::EDIT), compact('dungeonTrait', 'headers'));
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
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordUpdatedSuccessfully());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(DungeonTrait $trait)
	{
		$trait->delete();
	}
}
