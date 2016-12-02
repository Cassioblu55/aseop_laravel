@extends('layout.noMenu')

@section('required_scripts')
    <script type="text/javascript" src="{{asset("js/mapGenerator.js")}}"></script>
@stop

@section('content')

    @include('tiles.loading', ['title'=>'Creating Dungeon'])

    <div ng-controller="CreateMapController">
        <form class="hidden" action="{{url('/dungeons/generateWithMapAndTrapsCreated')}}" method="POST" id="form">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            {{method_field($headers->methodField)}}

            <textarea name="traps" type="text" ng-model="trapsString"></textarea>
            <textarea name="map"  type="text" ng-model="mapString"></textarea>
            <input name="size" ng-model="dungeon.size" type="text">
        </form>
    </div>

@stop


@section('scripts')
    <script>
        app.controller("CreateMapController", ['$scope', "$controller","$window","$http", function($scope, $controller, $window, $http) {

            angular.extend(this, $controller('TrapController', {$scope: $scope}));

            const TRAP_RESOURCE_URL = "{{url("/api/traps")}}";

            $scope.dungeon = {};
            $scope.dungeon.size = randomFromArray(['S','M','L']);

            $scope.setFromGet(TRAP_RESOURCE_URL,function(traps){
                $scope.trapOptions = traps;
                $scope.generateMap();
                setTraps();
                $scope.mapString = JSON.stringify($scope.map.getTiles());

            });

            $scope.$watchGroup(['mapString', 'trapsString'], function(vals){
                if(vals[0] && vals[1]){
                    $( "#form" ).submit();
                }
            });

            function setTraps(){
                var size = $scope.dungeon.size;
                var min = (size == "S") ? 1 : (size=='M') ? 2 : 3;
                var max = (size == "S") ? 3 : (size=='M') ? 5 : 8;
                var n = Math.random() * (max - min) + min;

                $scope.traps = [];
                for(var i=0; i<n; i++){
                    $scope.traps.push({});
                }
                $scope.setRandomTraps();

                addTrapsToMap();

                $scope.trapsString = getTrapString($scope.traps);

            }

            function addTrapsToMap(){
                for(var i=0; i<$scope.traps.length; i++){
                    //Set aviable options for each trap row
                    var trap = $scope.traps[i];
                    $scope.traps[i].rowOptions = $scope.map.activeRows(trap.column);
                    $scope.traps[i].columnOptions = $scope.map.activeColumns(trap.row);
                    if(trap.row && trap.column){
                        $scope.map.setTrap(trap.column,trap.row);
                    }
                }
            }

        }]);

    </script>
@stop