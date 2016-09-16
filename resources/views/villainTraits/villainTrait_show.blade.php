@extends('layout.show')

@section('show_title', $villainTrait->kind)

@section('show_body')
    <div class="container-fluid">

        <div class="">
            {{$villainTrait->type}}
        </div>

        <div class="showDisplay">
            {{$villainTrait->description}}
        </div>

    </div>


@stop

@section('scripts')
    <script>
        app.controller("VillainTraitShowController", ['$scope', "$controller", "$window" , function($scope, $controller) {
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

        }]);

    </script>

@stop