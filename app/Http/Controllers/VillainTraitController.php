<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\VillainTrait;

class VillainTraitController extends Controller
{
    const CONTROLLER_NAME = "villainTrait";
	
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
        $villainTrait = new VillainTrait();
        $headers = $this->getCreateHeaders();
        return view($this->getControllerView(Messages::EDIT), compact('villainTrait', 'headers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	    $villainTrait = new VillainTrait($request->all());
	    $villainTrait->setRequiredMissing();
	    return $this->validateAndRedirect($villainTrait, true);
    }

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = VillainTrait::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(VillainTrait $villainTrait)
    {
	    $headers = $this->getShowHeaders();
	    return view($this->getControllerView(Messages::SHOW), compact('villainTrait', 'headers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(VillainTrait $villainTrait)
    {
        $headers = $this->getUpdateHeaders($villainTrait->id);
        return view($this->getControllerView(Messages::EDIT), compact('villainTrait', 'headers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VillainTrait $villainTrait)
    {
        $villainTrait -> update($request->all());
	    return $this->validateAndRedirect($villainTrait, true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(VillainTrait $villainTrait)
    {
        $villainTrait->delete();
	    return redirect()->action($this->getIndexControllerAction(), self::sendSuccessfullyDeletedMesage());
    }
}