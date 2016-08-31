@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% trap.name || 'Trap' %>")

@section('controller', "TrapAddEditController")

@section('form-size', '6')

@section('required_scripts')
    <script type="text/javascript" src="{{asset('js/roll.js')}}"></script>
@stop

@section('form_body')

    @include("tiles.questions.text", ['field' =>'name'])

    @include("tiles.questions.textArea", ['field' =>'description'])

    @include('tiles.rolls.roll_display_panel')
    <input type="text" style="display: none;" name="rolls" ng-model="trap.rolls" />

    @include("tiles.questions.number", ['field' =>'weight'])

    @include('tiles.questions.publicPrivate')

    @include('tiles.rolls.roll_modal')
@stop

@section('back_location', url('/traps'))

@section('scripts')
    <script>
        app.controller("TrapAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));
            angular.extend(this, $controller('rollDisplayController', {$scope: $scope}));

            const CONFIG = {localResource: 'traps', defaultCheckObjectPresent: "{{$trap->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(trap){
                $scope.trap = trap;
                $scope.trap.weight = Number($scope.trap.weight);
                if($scope.trap.rolls) {
                    $scope.rollValues = getDiceValues($scope.trap.rolls);
                }
            });

            $scope.utils.runOnCreate(function(){
                $scope.trap = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.trap['public'] = n});
            });

            $scope.$watch("rollValues", function(){
                if($scope.rollValues){
                    if($scope.trap){
                        $scope.trap.rolls = getStringValues($scope.rollValues);
                    }
                }
            },true);

        }]);
    </script>
@stop