@extends('layout.show')

@section('show_title', $dungeonTrait->trait)

@section('show_body')
    <div class="container-fluid">

        <div class="">
            {{$dungeonTrait->type}}
        </div>

        <div class="showDisplay">
            {{$dungeonTrait->description}}
        </div>

    </div>


@stop

@section('scripts')
    <script>
        app.controller("DungeonTraitShowController", ['$scope', "$controller", "$window" , function($scope, $controller) {
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

        }]);

    </script>

@stop