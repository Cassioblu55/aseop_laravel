@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% dungeonTrait.trait || 'Trait' %>")

@section('controller', "DungeonTraitAddEditController")

@section('form-size', '6')

@section('form_body')
    @include("tiles.questions.text", ['field' =>'trait', 'required'=>true])

    @include("tiles.questions.selectFromModelArray", ['field' =>'type','required'=>true,'modelData' =>'validTypes'] )

    @include("tiles.questions.textArea", ['field' =>'description'])

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

            $scope.setFrmGet("{{url('/api/dungeonTraits/types')}}", function(data){
                $scope.validTypes = data;
            });

            $scope.utils.runOnCreate(function(){
                $scope.dungeonTrait = {};
                $scope.dungeonTrait.weight = 1;
                $scope.utils.getDefaultAccess(function(n){
                    $scope.dungeonTrait['public'] = n});
            });

        }]);
    </script>
@stop