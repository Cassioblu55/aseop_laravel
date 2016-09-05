@extends('layout.form')

@section('required_scripts')
    <script type="text/javascript" src="{{asset('js/roll.js')}}"></script>
@stop

@section('form_title', "$headers->createOrUpdate <% urbanEncounter.title || 'Forest Encounter' %>")

@section('controller', "UrbanEncounterAddEditController")

@section('form-size', '8')

@section('form_body')

    @include('tiles.questions.text', ['field' => 'title', 'required'=>true])

    @include('tiles.questions.textArea', ['field'=>'description'])

    <div class="form-group">
        <label>Number of Rolls</label>
        <input type="number" min=0 class="form-control" ng-model="numberOfRolls">
    </div>

    <!-- rolls -->
    <div class="row col-md-12" ng-show="numberOfRolls > 0">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <div class="panel-title pull-left">Rolls</div>
            </div>
            <div class="panel-body">
                <div ng-repeat="roll in rollValues">
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label>Amount</label>
                            <input type="number" min=0 class="form-control" ng-model="roll.amount">
                            <div>Min <%getDiceMin(roll)%></div>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Kind</label>
                            <input type="number" min=0 class="form-control" ng-model="roll.kind">
                            <div>Max <%getDiceMax(roll)%></div>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Modifer</label>
                            <input type="number" min=0 class="form-control" ng-model="roll.modifer">
                            <div>Average <%getDiceAverage(roll)%></div>
                        </div>

                        <div class="col-md-3" style="margin-top: 30px">
                            <% getDiceDisplay(roll) %>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <input class="hidden" name="rolls" ng-model="urbanEncounter.rolls">

    @include('tiles.questions.publicPrivate')
@stop

@section('back_location', url('/urbanEncounters'))

@section('scripts')
    <script>
        app.controller("UrbanEncounterAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));
            angular.extend(this, $controller('rollDisplayController', {$scope: $scope}));


            const CONFIG = {localResource: 'urbanEncounters', defaultCheckObjectPresent: "{{$urbanEncounter->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(urbanEncounter){
                $scope.urbanEncounter = urbanEncounter;
                $scope.rollValues = getDiceValues($scope.urbanEncounter.rolls);
                $scope.numberOfRolls = $scope.rollValues.length;
            });

            $scope.$watch('numberOfRolls', function (val) {
                if(val && $scope.rollValues){
                    while($scope.rollValues.length != val) {
                        if($scope.rollValues.length > val){
                            $scope.rollValues.pop();
                        }else if($scope.rollValues.length < val){
                            $scope.rollValues.push({});
                        }
                    }
                }
            });

            $scope.$watch("rollValues", function(val){
                if(val){
                    if($scope.urbanEncounter){
                        $scope.urbanEncounter.rolls = getStringValues(val);
                    }
                }
            },true);

            $scope.utils.runOnCreate(function(){
                $scope.urbanEncounter = {};
                $scope.urbanEncounter.rolls = [];
                $scope.utils.getDefaultAccess(function(n){
                    $scope.urbanEncounter['public'] = n});
            });

        }]);
    </script>
@stop