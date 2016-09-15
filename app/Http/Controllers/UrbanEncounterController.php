<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\UrbanEncounter;

class UrbanEncounterController extends Controller
{
    const CONTROLLER_NAME = "urbanEncounter";

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
        $urbanEncounter = new UrbanEncounter();
        $headers = $this->getCreateHeaders();
        return view($this->getControllerView("edit"), compact('urbanEncounter', 'headers'));
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
	    $urbanEncounter = UrbanEncounter::create($request->all());
	    return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact("urbanEncounter")));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function show(UrbanEncounter $urbanEncounter){
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(self::SHOW), compact('urbanEncounter', 'headers'));
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
        return view($this->getControllerView("edit"), compact('urbanEncounter', 'headers'));
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
        $urbanEncounter -> update($request->all());
	    return redirect()->action($this->getShowControllerAction(), self::addUpdateSuccessMessage(compact('urbanEncounter')));
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
    }
}