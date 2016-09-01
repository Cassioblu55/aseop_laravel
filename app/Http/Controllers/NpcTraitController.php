<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\NonPlayerCharacterTrait;

class NpcTraitController extends Controller
{
	const CONTROLLER_NAME = "npcTrait";

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
		$npcTrait = new NonPlayerCharacterTrait();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView("edit"), compact('npcTrait', 'headers'));
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
		NonPlayerCharacterTrait::create($request->all());
		return redirect()->action($this->getControllerAction('index'), self::sendRecordAddedSuccessfully());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(NonPlayerCharacterTrait $npcTrait)
	{

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
		return view($this->getControllerView("edit"), compact('npcTrait', 'headers'));
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
		$npcTrait -> update($request->all());
		return redirect()->action($this->getControllerAction('index'), self::sendRecordUpdatedSuccessfully());
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
	}
}
