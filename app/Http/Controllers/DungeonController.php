<?php


namespace App\Http\Controllers;

use App\Dungeon;
use App\GenericModel;
use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DungeonController extends Controller
{
	const CONTROLLER_NAME  = "dungeon";

	private $logging;

	public function __construct(){
		$this->setControllerNames(self::CONTROLLER_NAME);

		$this->logging = new Logging(self::class);

		$this->middleware('auth', ['except' => ['show']]);

		parent::__construct(self::class);

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
		$dungeon = new Dungeon($request->all());
		$dungeon->setRequiredMissing();
		return $this->validateAndRedirect($dungeon);
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
	    return $dungeon[Dungeon::ID];
    }

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = Dungeon::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
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
		return $this->validateAndRedirect($dungeon);
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