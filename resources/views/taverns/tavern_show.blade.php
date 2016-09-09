@extends('layout.app')

@section('content')

    <div class="container-fluid">
        @include('taverns.tavern_display', ['tavern' => $tavern])

        @include('npcs.npc_display', ['title' => "Owner: ".$tavern->owner->displayName(), 'npc'=> $tavern->owner, 'hide'=>true])
    </div>
@stop