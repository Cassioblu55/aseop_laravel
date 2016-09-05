@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% villainTrait.name || 'VillainTrait' %>")

@section('controller', "VillainTraitAddEditController")

@section('form-size', '6')

@section('form_body')
    @include('tiles.questions.text', ['field'=>'type'])

    @include('tiles.questions.textArea', ['field'=>'kind'])

    @include('tiles.questions.textArea', ['field'=>'description'])

    @include('tiles.questions.publicPrivate')

@stop

@section('back_location', url('/villainTraits'))

@section('scripts')
    <script>
        app.controller("VillainTraitAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'villainTraits', defaultCheckObjectPresent: "{{$villainTrait->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(villainTrait){
                $scope.villainTrait = villainTrait;
            });

            $scope.utils.runOnCreate(function(){
                $scope.villainTrait = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.villainTrait['public'] = n});
            });

        }]);
    </script>
@stop