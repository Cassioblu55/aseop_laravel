@extends('layout.show')

@section('show_title', $trap->name)

@section('show_body')
    <div class="container-fluid">
        <div class="showDisplay">
            {{$trap->description}}
        </div>

    </div>


@stop

@section('scripts')
    <script>
        app.controller("TrapShowController", ['$scope', "$controller", "$window" , function($scope, $controller) {
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

        }]);

    </script>

@stop