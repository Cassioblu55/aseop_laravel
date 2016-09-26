@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% npcTrait.trait || 'Trait' %>")

@section('controller', "NpcAddEditController")

@section('form-size', '6')

@section('form_body')
    @include("tiles.questions.text", ['field' =>'trait', 'required'=>true])

    @include("tiles.questions.selectFromModelArray", ['field' =>'type', 'required'=>true, 'modelData'=>"validTypes"])

    @include('tiles.questions.publicPrivate')
@stop

@section('back_location', url('/npcTraits'))

@section('scripts')
    <script>
        app.controller("NpcAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'npcTraits', defaultCheckObjectPresent: "{{$npcTrait->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(npcTrait){
                $scope.npcTrait = npcTrait;
            });

            $scope.setFromGet('{{url('/api/npcTraits/types')}}', function(data){
               $scope.validTypes = data;
            });

            $scope.utils.runOnCreate(function(){
                $scope.npcTrait = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.npcTrait['public'] = n});
            });

        }]);
    </script>
@stop