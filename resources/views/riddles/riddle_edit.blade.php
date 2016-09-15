@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% riddle.name || 'Riddle' %>")


@section('form-size', '6')

@section('form_body')
    @include("tiles.questions.text", ['field' =>'name', 'required'=>true])

    @include("tiles.questions.textArea", ['field' =>'riddle', 'required'=>true])

    @include("tiles.questions.textArea", ['field' =>'solution', 'required'=>true])

    @include("tiles.questions.textArea", ['field' =>'hint'])

    @include("tiles.questions.number", ['field' => 'weight', 'validation'=>'min=1', 'required'=>true])

    @include("tiles.questions.textArea", ['field' =>'other_information'])

    @include('tiles.questions.publicPrivate')
@stop

@section('scripts')
    <script>
        app.controller("RiddleAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'riddles', defaultCheckObjectPresent: "{{$riddle->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(riddle){
                $scope.riddle = riddle;
            });

            $scope.utils.runOnCreate(function(){
                $scope.riddle = {};
                $scope.riddle.weight = 1;
                $scope.utils.getDefaultAccess(function(n){
                    $scope.riddle['public'] = n});
            });

            const NPC_NAME_RESOURCE_URL = '{{url("/api/npcs/names")}}';
            $scope.setFromGet(NPC_NAME_RESOURCE_URL, function(data){
                $scope.npcs = data;
            });

        }]);
    </script>
@stop