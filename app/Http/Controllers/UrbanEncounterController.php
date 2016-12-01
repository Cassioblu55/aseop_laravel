<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\UrbanEncounter;

class UrbanEncounterController extends AbstractController
{
    const CONTROLLER_NAME = "urbanEncounter";

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
        $urbanEncounter = new UrbanEncounter();
        $headers = $this->getCreateHeaders();
        return view($this->getControllerView(Messages::EDIT), compact('urbanEncounter', 'headers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $urbanEncounter = new UrbanEncounter($request->all());
	    $urbanEncounter->setRequiredMissing();
	    return $this->validateStore($urbanEncounter);
    }

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = UrbanEncounter::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function show(UrbanEncounter $urbanEncounter){
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(Messages::SHOW), compact('urbanEncounter', 'headers'));
	}

	public function random(){
		return redirect()->action($this->getShowControllerAction(), [UrbanEncounter::random()]);
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(UrbanEncounter $urbanEncounter)
    {
        $headers = $this->getUpdateHeaders($urbanEncounter->id);
        return view($this->getControllerView(Messages::EDIT), compact('urbanEncounter', 'headers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UrbanEncounter $urbanEncounter)
    {
	    return $this->validateUpdate($request, $urbanEncounter);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UrbanEncounter $urbanEncounter)
    {
        $urbanEncounter->delete();
	    return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMessage());
    }
}