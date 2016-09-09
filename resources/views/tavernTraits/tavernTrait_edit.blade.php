@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% tavernTrait.trait || 'Trait' %>")

@section('controller', "TavernTraitAddEditController")

@section('form-size', '6')

@section('form_body')
    @include("tiles.questions.text", ['field' =>'trait', 'required' => true])

    @include("tiles.questions.select", ['field' =>'type','required'=>true, 'data' => ['first_name'=> 'First Name', 'last_name'=>'Last Name','type'=>'Type']])

    @include('tiles.questions.publicPrivate')
@stop

@section('back_location', url('/tavernTraits'))

@section('scripts')
    <script>
        app.controller("TavernTraitAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'tavernTraits', defaultCheckObjectPresent: "{{$tavernTrait->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(tavernTrait){
                $scope.tavernTrait = tavernTrait;
            });

            $scope.utils.runOnCreate(function(){
                $scope.tavernTrait = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.tavernTrait['public'] = n});
            });

        }]);
    </script>
@stop