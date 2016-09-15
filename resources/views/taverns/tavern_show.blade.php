
@extends('layout.show')

@section('show_title', $tavern->name)

@section('show_body')
    @include('taverns.tavern_display', ['tavern' => $tavern, 'title' => "Info"])

    @include('npcs.npc_display', ['title' => "Owner: ".$tavern->owner->displayName(), 'npc'=> $tavern->owner, 'hide'=>true])
@stop

@section('scripts')
    <script>
        app.controller("TavernShowController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

        }]);

    </script>

@stop