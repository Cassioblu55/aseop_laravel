<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Villain;

class VillainController extends Controller
{
    const CONTROLLER_NAME = "villain";

	private $logging;

    public function __construct(){
        $this->setControllerNames(self::CONTROLLER_NAME);

	    $this->logging = new Logging(self::class);

        $this->middleware('auth', ['except' => ['show']]);

	    parent::__construct(self::class);
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
	    $villain = new Villain($request->all());
	    $villain->setRequiredMissing();
	    return $this->validateStore($villain);
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
	    return $this->validateUpdate($request, $villain);
    }

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = Villain::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
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