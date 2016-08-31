@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% dungeonTrait.trait || 'Trait' %>")

@section('controller', "DungeonTraitAddEditController")

@section('form-size', '6')

@section('form_body')
    @include("tiles.questions.text", ['field' =>'trait'])

    @include("tiles.questions.text", ['field' =>'type'])

    @include("tiles.questions.textArea", ['field' =>'description'])

    @include("tiles.questions.number", ['field' =>'weight'])

    @include('tiles.questions.publicPrivate')
@stop

@section('back_location', url('/dungeonTraits'))

@section('scripts')
    <script>
        app.controller("DungeonTraitAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'dungeonTraits', defaultCheckObjectPresent: "{{$dungeonTrait->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(dungeonTrait){
                $scope.dungeonTrait = dungeonTrait;
                $scope.dungeonTrait.weight = Number($scope.dungeonTrait.weight);
            });

            $scope.utils.runOnCreate(function(){
                $scope.dungeonTrait = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.dungeonTrait['public'] = n});
            });

        }]);
    </script>
@stop