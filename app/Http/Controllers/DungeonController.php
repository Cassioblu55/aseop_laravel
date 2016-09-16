<?php


namespace App\Http\Controllers;

use App\Dungeon;
use App\Services\Messages;
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


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$dungeon = new Dungeon();
		$headers = $this->getCreateHeaders();
		return view($this->getControllerView(Messages::EDIT), compact('dungeon', 'headers'));

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
		$dungeon = Dungeon::create($request->all());
		return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact('dungeon')));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Dungeon $dungeon)
	{
		$headers = $this->getShowHeaders();
		return view($this->getControllerView(Messages::SHOW), compact('dungeon', 'headers'));
	}

    public function generate(){;
	    $dungeon = Dungeon::generate();
	    return view($this->getControllerView("createMap"), compact('dungeon'));
    }

    public function createWithIdReturn(Request $request){
	    $dungeon = Dungeon::create($request->all());
	    return $dungeon->id;
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
		return view($this->getControllerView(Messages::EDIT), compact('dungeon', 'headers'));
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
		return redirect()->action($this->getShowControllerAction(), self::addUpdateSuccessMessage(compact('dungeon')));
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
		return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
	}
}