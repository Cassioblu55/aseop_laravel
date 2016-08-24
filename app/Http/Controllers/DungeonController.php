<?php

namespace App\Http\Controllers;

use App\Dungeon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DungeonController extends Controller
{

    public function __construct(){
		$this->setControllerNameSpace($this->getControllerNameSpace());

	    $this->middleware('auth', ['except' => ['show']]);
    }

    private function getControllerNameSpace(){
    	return "dungeons";
    }


	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dungeons.dungeonIndex');
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
	    return view($this->getControllerNameSpace().".dungeonEdit", compact('dungeon', 'headers'));

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
	    $message = "Record Added Successfully";
	    return redirect()->action("DungeonController@index", ["successMessage" => $message]);
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
	    return view($this->getControllerNameSpace().".dungeonEdit", compact('dungeon', 'headers'));
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
	    $message = "Record Updated Successfully";
	    return redirect()->action("DungeonController@index", ["successMessage" => $message]);
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
