@extends('layout.noMenu')

@section('required_scripts')
    <script type="text/javascript" src="{{asset("js/mapGenerator.js")}}"></script>
@stop

@section('content')

    @include('tiles.loading', ['title'=>'Creating Dungeon'])

    <div ng-controller="CreateMapController">
        <div id="dungeon"  class="hidden">
            {{json_encode($dungeon)}}
        </div>
    </div>
@stop


@section('scripts')
    <script>
        app.controller("CreateMapController", ['$scope', "$controller","$window","$http", function($scope, $controller, $window, $http) {

            angular.extend(this, $controller('TrapController', {$scope: $scope}));

            const TRAP_RESOURCE_URL = "{{url("/api/traps")}}";

            $scope.dungeon = JSON.parse(document.getElementById('dungeon').innerHTML);

            $scope.setFromGet(TRAP_RESOURCE_URL,function(traps){
                $scope.trapOptions = traps;

                $scope.generateMap();
                setTraps();

                $scope.dungeon.traps = getTrapSting($scope.traps);
                $scope.dungeon['_token'] = "{{ csrf_token() }}";
                $scope.dungeon['_method'] = "POST";

                var req = {
                    method: 'POST',
                    url: "{{url('/dungeons/createWithIdReturn')}}",
                    data: $scope.dungeon
                };

                $http(req).then(function(response){
                    $window.location.href ="{{url('/dungeons')}}/"+response.data+"?successMessage=Dungeon Successfully added";
                });

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

                $scope.stringifyMap($scope.map.getTiles());
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