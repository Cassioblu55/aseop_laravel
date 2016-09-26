<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use App\TavernTrait;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;

class TavernTraitController extends Controller
{
	const CONTROLLER_NAME = "tavernTrait";
	
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
		$tavernTrait = new TavernTrait();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView(Messages::EDIT), compact('tavernTrait', 'headers'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$tavernTrait = new TavernTrait($request->all());
		$tavernTrait->setRequiredMissing();
		return $this->validateAndRedirect($tavernTrait, true);
	}

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = TavernTrait::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(TavernTrait $tavernTrait)
	{
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(Messages::SHOW), compact('tavernTrait', 'headers'));
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
		return view($this->getControllerView(Messages::EDIT), compact('tavernTrait', 'headers'));
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
		return $this->validateAndRedirect($tavernTrait, true);
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
		return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
	}
}
