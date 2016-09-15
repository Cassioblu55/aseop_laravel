@extends('layout.show')

@section('show_title', $settlementTrait->trait)

@section('show_body')
    <div class="container-fluid">

        <div class="">
            {{$settlementTrait->type}}
        </div>

    </div>


@stop

@section('scripts')
    <script>
        app.controller("SettlementTraitShowController", ['$scope', "$controller", "$window" , function($scope, $controller) {
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

        }]);

    </script>

@stop