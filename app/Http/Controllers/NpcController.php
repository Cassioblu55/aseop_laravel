<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NonPlayerCharacter;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class NpcController extends Controller
{

	const CONTROLLER_NAME = "npc";

	public function __construct(){
		$this->setControllerNames(self::CONTROLLER_NAME);

		$this->middleware('auth', ['except' => ['show']]);
	}

	public function index()
	{
		return view($this->getControllerView('index'));
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
		return view($this->getControllerView("edit"), compact('npc', 'headers'));
	}

	public function generate(){
		$npc = NonPlayerCharacter::generate();
		return redirect()->action($this->getControllerAction('edit'), [$npc]);
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
		NonPlayerCharacter::create($request->all());
		return redirect()->action($this->getControllerAction('index'), self::sendRecordAddedSuccessfully());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(NonPlayerCharacter $npc)
	{
		return view($this->getControllerView(self::SHOW), compact('npc'));
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
		$npc -> update($request->all());
		return redirect()->action($this->getControllerAction('index'), self::sendRecordUpdatedSuccessfully());
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
	}
}
