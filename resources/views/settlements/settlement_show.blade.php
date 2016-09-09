@extends('layout.app')

@section('content')
    <div class="container-fluid">
        @include('settlements.settlement_display', ['settlement' => $settlement])

        @include('npcs.npc_display', ['title' => "Ruler: ".$settlement->ruler->displayName(), 'npc'=> $settlement->ruler, 'hide'=>true])
    </div>
@stop