@extends('layout.show')

@section('show_title', $tavernTrait->trait)

@section('show_body')
    <div class="container-fluid">

        <div class="">
            {{$tavernTrait->type}}
        </div>

    </div>


@stop

@section('scripts')
    <script>
        app.controller("TavernTraitShowController", ['$scope', "$controller", "$window" , function($scope, $controller) {
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

        }]);

    </script>

@stop