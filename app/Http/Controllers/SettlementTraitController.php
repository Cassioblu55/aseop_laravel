<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\SettlementTrait;

class SettlementTraitController extends Controller
{
	const CONTROLLER_NAME = "settlementTrait";

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
		$settlementTrait = new SettlementTrait();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView(Messages::EDIT), compact('settlementTrait', 'headers'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$settlementTrait = new SettlementTrait($request->all());
		$settlementTrait->setRequiredMissing();
		return $this->validateStore($settlementTrait, true);
	}

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = SettlementTrait::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(SettlementTrait $settlementTrait)
	{
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(Messages::SHOW), compact('settlementTrait', 'headers'));
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
		return view($this->getControllerView(Messages::EDIT), compact('settlementTrait', 'headers'));
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
		return $this->validateUpdate($request, $settlementTrait, true);
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
		return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
	}
}
