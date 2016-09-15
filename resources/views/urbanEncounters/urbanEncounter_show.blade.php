@extends('layout.show')

@section('required_scripts')
    <script type="text/javascript" src="{{asset('js/roll.js')}}"></script>
@stop

@section('show_title', $urbanEncounter->title)

@section('show_body')

    <h4>Description</h4>
    <div><% urbanEncounter.description %></div>

@stop

@section('scripts')

    <script>
        app.controller("UrbanEncounterShowController", ['$scope', "$controller", function($scope, $controller){

            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'urbanEncounters', id: '{{$urbanEncounter->id}}'};
            $scope.utils = $scope.CreateShowUtil(CONFIG);

            $scope.utils.setDisplay(function(data){
                $scope.urbanEncounter = data;
                $scope.urbanEncounter.description = getRolls(data.description, data.rolls);
            });


        }]);

    </script>

@stop