@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% settlementTrait.trait || 'Trait' %>")

@section('controller', "SettlementTraitAddEditController")

@section('form-size', '6')

@section('form_body')
    @include("tiles.questions.text", ['field' =>'trait'])

    @include("tiles.questions.text", ['field' =>'type'])

    @include('tiles.questions.publicPrivate')
@stop

@section('back_location', url('/settlementTraits'))

@section('scripts')
    <script>
        app.controller("SettlementTraitAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'settlementTraits', defaultCheckObjectPresent: "{{$settlementTrait->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(settlementTrait){
                $scope.settlementTrait = settlementTrait;
            });

            $scope.utils.runOnCreate(function(){
                $scope.settlementTrait = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.settlementTrait['public'] = n});
            });

        }]);
    </script>
@stop