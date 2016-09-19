<?php

namespace App\Http\Controllers;

use App\Services\Messages;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Trap;
use Illuminate\Support\Facades\Auth;


class TrapController extends Controller
{

	const defaultValidation = [
		'weight' => 'numeric|min:1'
	];

	const CONTROLLER_NAME  = "trap";

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
	    $trap = new Trap();
	    $headers = $this->getCreateHeaders();
	    return view($this->getControllerView(Messages::EDIT), compact('trap', 'headers'));
    }

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = Trap::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
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
	    $trap = Trap::create($request->all());
	    return redirect()->action($this->getShowControllerAction(), self::addAddedSuccessMessage(compact("trap")));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Trap $trap)
    {
	    $headers = $this->getShowHeaders();
	    return view($this->getControllerView(Messages::SHOW), compact('trap', 'headers'));
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
	    return view($this->getControllerView(Messages::EDIT), compact('trap', 'headers'));
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
	    return redirect()->action($this->getShowControllerAction(), self::addUpdateSuccessMessage(compact('trap')));
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
	    return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
    }
}
