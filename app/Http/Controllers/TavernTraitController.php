<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TavernTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;

class TavernTraitController extends Controller
{
	const CONTROLLER_NAME = "tavernTrait";

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
		$tavernTrait = new TavernTrait();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView("edit"), compact('tavernTrait', 'headers'));
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
		TavernTrait::create($request->all());
		return redirect()->action($this->getControllerAction('index'), self::sendRecordAddedSuccessfully());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(TavernTrait $tavenTrait)
	{

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(TavernTrait $tavernTrait)
	{
		$headers = $this->getUpdateHeaders($tavernTrait->id);
		return view($this->getControllerView("edit"), compact('tavernTrait', 'headers'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, TavernTrait $tavernTrait)
	{
		$tavernTrait -> update($request->all());
		return redirect()->action($this->getControllerAction('index'), self::sendRecordUpdatedSuccessfully());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(TavernTrait $tavernTrait)
	{
		$tavernTrait->delete();
	}
}
