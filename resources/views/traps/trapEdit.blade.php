@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% trap.name || 'Trap' %>")

@section('controller', "TrapAddEditController")

@section('form-size', '6')

@section('required_scripts')
    <script type="text/javascript" src="{{asset('js/roll.js')}}"></script>
@stop

@section('form_body')
    <div class="form-group">
        <label>Name</label> <input type="text" class="form-control" required="required" name="name" ng-model="trap.name" placeholder="Name" />
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textArea type="text" class="form-control" rows="6" name="description" ng-model="trap.description" placeholder="Description"></textArea>
    </div>
    @include('tiles.rolls.roll_display_panel')
    <input type="text" style="display: none;" name="rolls" ng-model="trap.rolls" />
    <div class="form-group">
        <div class="{{ $errors->has('weight') ? 'has-error' : '' }}">
            <label class="control-label">Weight</label>
            <input type="number" class="form-control" name="weight" ng-model="trap.weight" placeholder="Weight" min="0" />
            @include('tiles.error', ['errorName' => 'weight'])
        </div>
        <p class="help-block">Will determine how often trap will appear randomly when making dungons</p>
    </div>


    <div class="form-group">
        <label for="public">Public or Private</label>
        <select class="form-control" id="public" name="public" ng-model="trap.public">
            <option ng-selected="trap.public=='1'" value="1">Public</option>
            <option  ng-selected="trap.public=='0'" value="0">Private</option>
        </select>
    </div>
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