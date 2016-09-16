<?php

namespace App\Http\Controllers;

use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Spell;

class SpellController extends Controller
{
    const CONTROLLER_NAME = "spell";

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
        $spell = new Spell();
        $headers = $this->getCreateHeaders();
        return view($this->getControllerView(Messages::EDIT), compact('spell', 'headers'));
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
	    $spell = Spell::create($request->all());
	    return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact("spell")));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Spell $spell)
    {
	    $headers = $this->getShowHeaders();
	    return view($this->getControllerView(Messages::SHOW), compact('spell', 'headers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Spell $spell)
    {
        $headers = $this->getUpdateHeaders($spell->id);
        return view($this->getControllerView(Messages::EDIT), compact('spell', 'headers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Spell $spell)
    {
        $spell -> update($request->all());
	    return redirect()->action($this->getShowControllerAction(), self::addUpdateSuccessMessage(compact('spell')));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Spell $spell)
    {
        $spell->delete();
	    return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
    }
}