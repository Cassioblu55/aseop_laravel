<?php

namespace App\Http\Controllers;

use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Villain;

class VillainController extends Controller
{
    const CONTROLLER_NAME = "villain";

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
        $villain = new Villain();
        $headers = $this->getCreateHeaders();
        return view($this->getControllerView(Messages::EDIT), compact('villain', 'headers'));
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

	    $villain = Villain::create($request->all());
	    return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact("villain")));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Villain $villain)
    {
	    $headers = $this->getShowHeaders();
	    return view($this->getControllerView(Messages::SHOW), compact('villain', 'headers'));
    }

    public function generate(){
    	$villain = Villain::generate();
	    return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact('villain')));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Villain $villain)
    {
        $headers = $this->getUpdateHeaders($villain->id);
        return view($this->getControllerView(Messages::EDIT), compact('villain', 'headers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Villain $villain)
    {
        $villain -> update($request->all());
	    $dataHash = ['villain' => $villain];
        return redirect()->action($this->getShowControllerAction(), self::addUpdateSuccessMessage($dataHash));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Villain $villain)
    {
        $villain->delete();
	    return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
    }
}