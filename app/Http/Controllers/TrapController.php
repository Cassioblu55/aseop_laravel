<?php

namespace App\Http\Controllers;

use App\Services\Logging;
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
	    $trap = new Trap($request->all());
	    $trap->setRequiredMissing();
	    return $this->validateStore($trap, true);
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
	    return $this->validateUpdate($request, $trap, true);
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
