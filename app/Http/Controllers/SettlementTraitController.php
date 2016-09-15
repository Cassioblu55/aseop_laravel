<?php

namespace App\Http\Controllers;

use App\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\SettlementTrait;

class SettlementTraitController extends Controller
{
	const CONTROLLER_NAME = "settlementTrait";

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
		$settlementTrait = new SettlementTrait();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView("edit"), compact('settlementTrait', 'headers'));
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
		SettlementTrait::create($request->all());
		return redirect()->action($this->getControllerAction('index'), self::sendRecordAddedSuccessfully());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(SettlementTrait $settlementTrait)
	{

	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(SettlementTrait $settlementTrait)
	{
		$headers = $this->getUpdateHeaders($settlementTrait->id);
		return view($this->getControllerView("edit"), compact('settlementTrait', 'headers'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, SettlementTrait $settlementTrait)
	{
		$settlementTrait -> update($request->all());
		return redirect()->action($this->getControllerAction('index'), self::sendRecordUpdatedSuccessfully());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(SettlementTrait $settlementTrait)
	{
		$settlementTrait->delete();
	}
}
