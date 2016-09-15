<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\ForestEncounter;

class ForestEncounterController extends Controller
{
    const CONTROLLER_NAME = "forestEncounter";

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
        $forestEncounter = new ForestEncounter();
        $headers = $this->getCreateHeaders();
        return view($this->getControllerView("edit"), compact('forestEncounter', 'headers'));
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
	    $forestEncounter = ForestEncounter::create($request->all());
	    return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact("forestEncounter")));
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
	    return view($this->getControllerView(self::SHOW), compact('forestEncounter', 'headers'));
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
        return view($this->getControllerView("edit"), compact('forestEncounter', 'headers'));
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
        $forestEncounter -> update($request->all());
	    return redirect()->action($this->getShowControllerAction(), self::addUpdateSuccessMessage(compact('forestEncounter')));
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
    }
}