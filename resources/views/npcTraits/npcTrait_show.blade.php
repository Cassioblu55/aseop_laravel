@extends('layout.show')

@section('show_title', $npcTrait->trait)

@section('show_body')
    <div class="container-fluid">

        <div class="">
            {{$npcTrait->type}}
        </div>

    </div>


@stop

@section('scripts')
    <script>
        app.controller("NpcTraitShowController", ['$scope', "$controller", "$window" , function($scope, $controller) {
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

        }]);

    </script>

@stop