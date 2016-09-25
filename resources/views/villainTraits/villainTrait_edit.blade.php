@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% villainTrait.name || 'VillainTrait' %>")

@section('controller', "VillainTraitAddEditController")

@section('form-size', '6')

@section('form_body')

    <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
        <label class="control-label" for="type_inputDiv">Type</label>
        <select id="type_inputDiv" required="required" class="form-control" name="type" ng-model="villainTrait.type">
            <option disabled="disabled" value="">Choose One</option>
            <option ng-repeat="key in getKeysFromHash(validKinds) " value="<% key %>" ng-selected="villainTrait.type == key"><%capitalizeEachWord(key)%></option>
        </select>
        @include('tiles.error', ['errorName' => "type"])
    </div>

    <div class="form-group {{ $errors->has('kind') ? 'has-error' : '' }}">
        <label class="control-label" for="kind_inputDiv">Kind</label>
        <select id="kind_inputDiv" required="required" class="form-control" name="kind" ng-model="villainTrait.kind">
            <option disabled="disabled" value="">Choose One</option>
            <option ng-repeat="value in currentValidKinds " value="<% value %>" ng-selected="villianTrait.kind == value"><% capitalizeEachWord(value)%></option>
        </select>
        @include('tiles.error', ['errorName' => "kind"])
    </div>

    @include('tiles.questions.textArea', ['field'=>'description'])

    @include('tiles.questions.publicPrivate')

@stop

@section('back_location', url('/villainTraits'))

@section('scripts')
    <script>
        app.controller("VillainTraitAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'villainTraits', defaultCheckObjectPresent: "{{$villainTrait->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(villainTrait){
                console.log(villainTrait);
                $scope.villainTrait = villainTrait;
            });

            $scope.utils.runOnCreate(function(){
                $scope.villainTrait = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.villainTrait['public'] = n});
            });


            $scope.setFromGet("{{url('/api/villainTraits/kinds')}}", function(data){
                $scope.validKinds = data;
                if($scope.villainTrait.type){
                    $scope.currentValidKinds =  $scope.validKinds[$scope.villainTrait.type];

                }
            });

            String.prototype.toCamelCase = function() {
                return this.replace(/^([A-Z])|\s(\w)/g, function(match, p1, p2, offset) {
                    if (p2) return p2.toUpperCase();
                    return p1.toLowerCase();
                });
            };

            $scope.getKeysFromHash = function(hash){
                if(hash){
                    return Object.keys(hash);
                }
                return null;
            };

            $scope.$watch('villainTrait.type', function(val){
                if(val && $scope.validKinds){
                    $scope.villainTrait.kind = '';
                    $scope.currentValidKinds =  $scope.validKinds[val];
                }
            });

        }]);
    </script>
@stop