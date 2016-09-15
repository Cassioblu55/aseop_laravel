@extends('layout.show')

@section('required_scripts')
    <script type="text/javascript" src="{{asset('js/roll.js')}}"></script>
@stop

@section('show_title', $forestEncounter->title)

@section('show_body')

    <h4>Description</h4>
    <div><% forestEncounter.description %></div>

@stop

@section('scripts')

    <script>
        app.controller("ForestEncounterShowController", ['$scope', "$controller", function($scope, $controller){

            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'forestEncounters', id: '{{$forestEncounter->id}}'};
            $scope.utils = $scope.CreateShowUtil(CONFIG);

            $scope.utils.setDisplay(function(data){
                $scope.forestEncounter = data;
                $scope.forestEncounter.description = getRolls(data.description, data.rolls);
            });


        }]);

    </script>

@stop