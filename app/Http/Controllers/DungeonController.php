<?php

namespace App\Http\Controllers;

use App\Dungeon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DungeonController extends Controller
{
	const CONTROLLER_NAME  = "dungeon";

	public function __construct(){
	    $this->setControllerNames(self::CONTROLLER_NAME);

	    $this->middleware('auth', ['except' => ['show']]);
    }

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view($this->getControllerView("index"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	    $dungeon = new Dungeon();
	    $headers = $this->getCreateHeaders();
	    return view($this->getControllerView("edit"), compact('dungeon', 'headers'));

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
	    Dungeon::create($request->all());
	    return redirect()->action($this->getControllerAction("index"), self::sendRecordAddedSuccessfully());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Dungeon $dungeon)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Dungeon $dungeon)
    {
        $headers = $this->getUpdateHeaders($dungeon->id);
	    return view($this->getControllerView("edit"), compact('dungeon', 'headers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dungeon $dungeon)
    {
	    $dungeon -> update($request->all());
	    return redirect()->action($this->getControllerAction("index"), self::sendRecordUpdatedSuccessfully());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dungeon $dungeon)
    {
	    $dungeon->delete();
    }
}
