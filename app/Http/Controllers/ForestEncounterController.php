<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\ForestEncounter;

class ForestEncounterController extends AbstractController
{
    const CONTROLLER_NAME = "forestEncounter";

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
        $forestEncounter = new ForestEncounter();
        $headers = $this->getCreateHeaders();
        return view($this->getControllerView(Messages::EDIT), compact('forestEncounter', 'headers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $forestEncounter = new ForestEncounter($request->all());
	    $forestEncounter->setRequiredMissing();
	    return $this->validateStore($forestEncounter);
    }

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = ForestEncounter::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ForestEncounter $forestEncounter)
    {
    	$headers = $this->getShowHeaders();
	    return view($this->getControllerView(Messages::SHOW), compact('forestEncounter', 'headers'));
    }

	public function random(){
		return redirect()->action($this->getShowControllerAction(), [ForestEncounter::random()]);
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ForestEncounter $forestEncounter)
    {
        $headers = $this->getUpdateHeaders($forestEncounter->id);
        return view($this->getControllerView(Messages::EDIT), compact('forestEncounter', 'headers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ForestEncounter $forestEncounter)
    {
	    return $this->validateUpdate($request, $forestEncounter);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ForestEncounter $forestEncounter)
    {
        $forestEncounter->delete();
	    return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMessage());
    }
}