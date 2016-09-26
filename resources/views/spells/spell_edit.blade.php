@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% spell.name || 'Spell' %>")

@section('controller', "SpellAddEditController")

@section('form-size', '6')

@section('form_body')
    @include("tiles.questions.text", ['field' =>'name', 'required' => true])

    @include("tiles.questions.selectFromModelArray", ['field' =>'type', 'required' => true, 'modelData' => 'validTypes'])

    @include("tiles.questions.selectFromModelArray", ['field' =>'class', 'required' => true, 'modelData' => 'validClasses'])

    @include("tiles.questions.number", ['field' =>'level', 'required'=> true, 'validation'=> 'min=0 max=9'])

    @include("tiles.questions.text", ['field' =>'casting_time'])

    @include("tiles.questions.number", ['field' =>'range', 'required'=> true, 'validation' => 'min = 0'])

    @include("tiles.questions.text", ['field' =>'components'])

    @include("tiles.questions.text", ['field' =>'duration'])

    @include("tiles.questions.textArea", ['field' =>'description', 'required' => true])

    @include('tiles.questions.publicPrivate')
@stop

@section('back_location', url('/spells'))

@section('scripts')
    <script>
        app.controller("SpellAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'spells', defaultCheckObjectPresent: "{{$spell->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(spell){
                $scope.spell = spell;
            });

            $scope.setFromGet('{{url('/api/spells/classes')}}', function (data) {
               $scope.validClasses = data;
            });

            $scope.setFromGet('{{url('/api/spells/types')}}', function (data) {
                $scope.validTypes = data;
            });


            $scope.utils.runOnCreate(function(){
                $scope.spell = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.spell['public'] = n});
            });

        }]);
    </script>
@stop