@extends('layout.show')

@section('required_scripts')
    <script src="{{asset('js/mapGenerator.js')}}" type="text/javascript"></script>
@stop

@section('show_title', $dungeon->name)

@section('show_body')
    <div class="row">
        <div class="col-md-6">
            @if($dungeon->purpose)
                <h4>Purpose</h4>
                <div>{{$dungeon->purpose}}</div>
            @endif

            @if($dungeon->history)
                <h4>History</h4>
                <div>{{$dungeon->history}}</div>
            @endif

            @if($dungeon->location)
                <h4>Location</h4>
                <div>{{$dungeon->location}}</div>
            @endif

            @if($dungeon->creator)
                <h4>Creator</h4>
                <div>{{$dungeon->creator}}</div>
            @endif

            <div ng-show='traps.length >0'>
                <h4>Traps</h4>
                <div ng-repeat="trap in traps">
                    <label><%getTrapDisplay(trap).title%></label>
                    <p><%getTrapDisplay(trap).description%></p>
                </div>
            </div>

            @if($dungeon->other_information)
                <h4>Other Information</h4>
                <div class="showDisplay">{{$dungeon->other_information}}</div>
            @endif
        </div>

        <div class="col-md-6">
            <h4>Map</h4>
            <div>
                <canvas id="mapDisplay" class="dungeon_map" style="width: 384px; height: 384px;">
                    Your browser does not support the HTML5 canvas tag.
                </canvas>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script>
        app.controller("DungeonShowController", ['$scope', "$controller", function($scope, $controller) {
            angular.extend(this, $controller('TrapController', {$scope: $scope}));

            const CONFIG = {'localResource': 'dungeons', 'id' : "{{$dungeon->id}}"}

            $scope.utils =  $scope.CreateShowUtil(CONFIG);

            $scope.setFromGet("{{url('/api/traps')}}",function(traps){
                $scope.trapOptions = traps;
            });

            $scope.utils.setDisplay(function(dungeon){
                $scope.dungeon = dungeon;
                $scope.traps = stringToTraps(dungeon.traps);
                $scope.getParsedMap = function(){return JSON.parse($scope.dungeon.map);}
                drawMap($scope.getParsedMap());
            });


        }]);

    </script>

@stop

