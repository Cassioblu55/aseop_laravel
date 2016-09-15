@extends('layout.show')

@section('show_title', $settlement->name)

@section('show_body')
    <div class="container-fluid">
        @include('settlements.settlement_display', ['settlement' => $settlement, 'title' => "Info"])

        @include('npcs.npc_display', ['title' => "Ruler: ".$settlement->ruler->displayName(), 'npc'=> $settlement->ruler, 'hide'=>true])
    </div>
@stop

@section('scripts')
    <script>
        app.controller("SettlementShowController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

        }]);

    </script>
@stop
