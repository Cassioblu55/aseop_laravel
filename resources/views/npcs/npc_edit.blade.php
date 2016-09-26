@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% (npc.first_name) ? npc.first_name+' '+npc.last_name : 'Non Player Character' %>")

@section('controller', "NpcAddEditController")

@section('form-size', '7')

@section('form_body')
    <div class="row">
        <div class="col-md-6">
            @include("tiles.questions.text", ['field' =>'first_name', 'validation'=>'required = "required"'])
        </div>
        <div class="col-md-6">
            @include("tiles.questions.text", ['field' =>'last_name'])
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            @include("tiles.questions.number", ['field' =>'age', 'validation'=>'min=0', 'required'=> true])
        </div>
        <div class="col-md-6">
            @include("tiles.questions.select", ['field'=>'sex', 'data'=>['M'=> 'Male', 'F' => 'Female', 'N' =>'None','O' =>'Other'], 'required'=> true])
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-sm-6 form-group">
                    <label for="feet">Feet</label>
                    <input type="number" id="feet" required="required" class="form-control" min="0" ng-model="npc.feet" placeholder="Feet" />
                </div>
                <div class="col-sm-6 form-group">
                    <label for="inches">Inches</label>
                    <input type="number" id="inches" required="required" value="0" class="form-control" min="0" max="11" ng-model="npc.inches" placeholder="Inches" />
                </div>

                <input class="hidden" type="number" name="height" ng-model="npc.height"/>
            </div>

        </div>

        <div class="col-md-6">
            <div class="form-group {{ $errors->has("weight") ? 'has-error' : '' }}">
                <label for="weight">Weight</label>
                <div class="input-group">
                    <input type="number" id="weight" class="form-control" required="required" name="weight" min="1" ng-model="npc.weight" placeholder="Weight" />
                    <div class="input-group-addon">lbs</div>
                </div>
                @include('tiles.error', ['errorName' => "weight"])
            </div>
        </div>
    </div>

    @include("tiles.questions.text", ['field' =>'flaw'])
    @include("tiles.questions.text", ['field' =>'interaction'])
    @include("tiles.questions.text", ['field' =>'mannerism'])
    @include("tiles.questions.text", ['field' =>'bond'])
    @include("tiles.questions.text", ['field' =>'appearance'])
    @include("tiles.questions.text", ['field' =>'talent'])
    @include("tiles.questions.text", ['field' =>'ideal'])
    @include("tiles.questions.text", ['field' =>'ability'])

    @include("tiles.questions.textArea", ['field' =>'other_information'])

    @include('tiles.questions.publicPrivate')
@stop

@section('back_location', url('/npcs'))

@section('scripts')
    <script>
        app.controller("NpcAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'npcs', defaultCheckObjectPresent: "{{$npc->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(npc){
                $scope.npc = npc;
                $scope.npc.age = Number($scope.npc.age);
                $scope.npc.weight = Number($scope.npc.weight);
                $scope.npc.feet = Math.floor(Number($scope.npc.height)/12);
                $scope.npc.inches = Number($scope.npc.height)%12;
            });

            $scope.utils.runOnCreate(function(){
                $scope.npc = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.npc['public'] = n});
            });


            $scope.$watchGroup(['npc.feet', 'npc.inches'], function(values){
                if(values && $scope.npc){
                    $scope.npc.height = combineFeetAndInches(values[0], values[1]);
                }
            });

            function combineFeetAndInches(feet, inches){
                return (feet*12) + inches;
            }

        }]);
    </script>
@stop