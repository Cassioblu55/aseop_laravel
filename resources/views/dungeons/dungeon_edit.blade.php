@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% dungeon.name || 'Dungeon' %>")

@section('controller', "DungeonAddEditController")

@section('form-size', '12')

@section('required_scripts')
    <script type="text/javascript" src="{{asset("js/mapGenerator.js")}}"></script>
@stop

@section('form_body')

    <div class="panel-body">
        <div class="col-md-6">
            @include("tiles.questions.text", ['field' =>'name'])

            @include("tiles.questions.text", ['field' =>'purpose'])

            @include("tiles.questions.text", ['field' =>'history'])

            @include("tiles.questions.text", ['field' =>'location'])

            @include("tiles.questions.text", ['field' =>'creator'])

            <div class="form-group">
                <label for="size">Size</label> <select class="form-control" ng-model="dungeon.size" name="size">
                    <option value="">Any</option>
                    <option ng-selected="dungeon.size== 'S'" value="S">Smalll</option>
                    <option ng-selected="dungeon.size== 'M'" value="M">Medium</option>
                    <option ng-selected="dungeon.size== 'L'" value="L">Large</option>
                </select>
            </div>

            <div class="form-group">
                <label>Number of Traps</label>
                <input class="form-control" type="number" ng-model="trapNumber" placeholder="Number of Traps" />
            </div>
            <div class="row" ng-show="traps.length >0">
                <div class="col-md-4">
                    <lable>Kind</lable>
                </div>
                <div class="col-md-4">
                    <lable>Column</lable>
                </div>
                <div class="col-md-4">
                    <lable>Row</lable>
                </div>
            </div>
            <div ng-repeat="trap in traps">
                <div class="form-group row">
                    <div class="col-md-4">
                        <select ng-model="trap.id" class="form-control">
                            <option value="">Any</option>
                            <option ng-repeat="trapOption in trapOptions"
                                    value="<% trapOption.id %>"> <% trapOption.name %></option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select ng-model="trap.column" class="form-control">
                            <option value="">Any</option>
                            <option ng-repeat="(value, letter) in trap.columnOptions"
                                    value="<%value%>"><%letter%></option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select ng-model="trap.row" class="form-control">
                            <option value="">Any</option>
                            <option ng-repeat="(key, value) in trap.rowOptions"
                                    value="<%key%>"><%value%></option>
                        </select>
                    </div>
                </div>
            </div>

            @include("tiles.questions.textArea", ['field' =>'other_information'])

            @include('tiles.questions.publicPrivate')

        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="map">Map</label>
                <div>
                    <canvas id="mapDisplay" class="dungeon_map"
                            style="width: 384px; height: 384px;">
                        Your browser does not support the HTML5 canvas tag.
                    </canvas>
                </div>
                <button type="button" class="btn btn-primary"
                        ng-click="generateMap()">New Map</button>
                <button type="button" class="btn btn-primary"
                        ng-click="setRandomTraps()">Set traps</button>
            </div>
            <div ng-repeat="trap in traps">
                <label><% getTrapDisplay(trap).title %></label>
                <p><% getTrapDisplay(trap).description %></p>
            </div>
        </div>
    </div>
    <textarea style="display: none" type="text" ng-model="dungeon.map" name="map"></textarea>
    <input style="display: none" type="text" ng-model="dungeon.traps" type="text" name="traps" />
@stop

@section('back_location', url('/dungeons'))

@section('scripts')
    <script>
        app.controller("DungeonAddEditController", ['$scope', "$controller", function($scope, $controller){

            angular.extend(this, $controller('TrapController', {$scope: $scope}))

            const TRAP_RESOURCE_URL = "{{url("/api/traps")}}";

            $scope.setFromGet(TRAP_RESOURCE_URL,loadTraps);
            function loadTraps(traps){$scope.trapOptions = traps;}

            $scope.letters = letters;

            const CONFIG = {localResource: 'dungeons', defaultCheckObjectPresent: "{{$dungeon->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(dungeon){
                $scope.dungeon = dungeon;
                $scope.map = map(JSON.parse(dungeon.map));
                $scope.map.setActiveTiles();
                $scope.traps = stringToTraps(dungeon.traps);
                $scope.trapNumber = ($scope.traps) ? $scope.traps.length : 0;
            });

            $scope.utils.runOnCreate(function(){
                $scope.dungeon = {};
                $scope.utils.getDefaultAccess(function(n){
                $scope.dungeon['public'] = n});
                $scope.dungeon.size = getRandomSize();
            });

            $scope.$watch('dungeon.size', function(val, oldVal){
                //If map size is being changed generate a new map
                if(oldVal && val && val != ''){
                    $scope.generateMap();
                }
            });

            $scope.traps = [];
            $scope.$watch('trapNumber', function(newVal,oldVal){
                if(!$scope.traps){$scope.traps=[];}
                if(newVal != $scope.traps.length){
                    while($scope.traps.length < newVal){
                        var trap = {};
                        $scope.traps.push(trap);
                    }
                    while($scope.traps.length > newVal){
                        $scope.traps.pop();
                    }

                }
            });

            $scope.$watch('traps', function(val){
                if(val && val.length > 0){
                    //Remove all traps in map
                    $scope.map.removeTraps();
                    for(var i=0; i<val.length; i++){
                        //Set aviable options for each trap row
                        var trap = val[i];
                        val[i].rowOptions = $scope.map.activeRows(trap.column);
                        val[i].columnOptions = $scope.map.activeColumns(trap.row);
                        if(trap.row && trap.column){
                            $scope.map.setTrap(trap.column,trap.row);
                        }
                    }
                    $scope.stringifyMap($scope.map.getTiles());
                    $scope.dungeon.traps = getTrapString(val);
                }
            }, true);

            $scope.$watch('dungeon.map', function(val){
                if(val){
                    drawMap(JSON.parse(val));
                }
            });

        }]);
    </script>
@stop