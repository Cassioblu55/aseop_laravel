<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Trap;
use Illuminate\Support\Facades\Auth;


class TrapController extends Controller
{

	const defaultValidation = [
		'weight' => 'numeric|min:1'
	];

	public function __construct(){
		$this->setControllerNameSpace($this->getControllerNameSpace());

		$this->middleware('auth', ['except' => ['show']]);
	}

	private function getControllerNameSpace(){
		return "traps";
	}

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	    return view('traps.trapIndex');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	    $trap = new Trap();
	    $headers = $this->getCreateHeaders();
	    return view($this->getControllerNameSpace().".trapEdit", compact('trap', 'headers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $this->validate($request, self::defaultValidation);

	    $request['owner_id'] = Auth::user()->id;
	    $request['approved'] = false;
	    Trap::create($request->all());
	    return redirect()->action("TrapController@index", self::sendRecordAddedSuccessfully());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Trap $trap)
    {
	    $headers = $this->getUpdateHeaders($trap->id);
	    return view($this->getControllerNameSpace().".trapEdit", compact('trap', 'headers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Trap $trap)
    {
	    $this->validate($request, self::defaultValidation);

	    $trap -> update($request->all());
	    return redirect()->action("TrapController@index", self::sendRecordUpdatedSuccessfully());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Trap $trap)
    {
        $trap->delete();
    }
}
