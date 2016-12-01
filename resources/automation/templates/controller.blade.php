<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Base_name;

class Base_nameController extends AbstractController
{
    const CONTROLLER_NAME = "base_name";

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
        $base_name = new Base_name();
        $headers = $this->getCreateHeaders();
        return view($this->getControllerView(self::EDIT), compact('base_name', 'headers'));
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
        Base_name::create($request->all());
        return redirect()->action($this->getControllerAction(self::INDEX), self::sendRecordAddedSuccessfully());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Base_name $base_name)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Base_name $base_name)
    {
        $headers = $this->getUpdateHeaders($base_name->id);
        return view($this->getControllerView(self::EDIT), compact('base_name', 'headers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Base_name $base_name)
    {
        $base_name -> update($request->all());
        return redirect()->action($this->getControllerAction(self::INDEX), self::sendRecordUpdatedSuccessfully());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Base_name $base_name)
    {
        $base_name->delete();
    }
}