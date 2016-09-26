<?php

namespace App\Http\Controllers;

use App\Services\Logging;
use App\Services\Messages;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Spell;

class SpellController extends Controller
{
    const CONTROLLER_NAME = "spell";

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
	    $spell = new Spell($request->all());
	    $spell->setRequiredMissing();
	    return $this->validateStore($spell);
    }

	public function upload(){
		$headers = $this->getUploadHeaders();
		return view($this->getControllerView(Messages::UPLOAD), compact('headers'));
	}

	public function saveBatch(Request $request){
		$response = Spell::upload($request->fileToUpload);
		return redirect()->action($this->getIndexControllerAction(), self::sendRecordAddedSuccessfully($response));
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
	    return $this->validateUpdate($request, $spell);
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