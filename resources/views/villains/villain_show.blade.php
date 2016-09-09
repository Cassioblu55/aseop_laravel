@extends('layout.app')

@section('content')
    <div ng-controller="VillainShowController">
        <div class="container-fluid">

            @include('npcs.npc_display', ['npc' => $villain->npc, 'hide' => true])

            @include('villains.villain_display', ['villain'=>$villain, 'hide'=>true])

            @if(count($villain->npc->owns) >0)
                <div class="panel panel-default" >
                    <div class="panel-heading clearfix">
                        <h1 class="panel-title pull-left" style="padding-top: 7.5px;">Taverns Owned</h1>
                        <button class="btn btn-primary pull-right" ng-click="showTaverns = !showTaverns"><% (showTaverns) ? 'Hide' : 'Show' %></button>
                    </div>
                    <div class="panel-body" ng-show="showTaverns">
                        @foreach($villain->npc->owns as $tavern)
                            @include('taverns.tavern_display', ['tavern'=>$tavern])
                        @endforeach
                    </div>
                </div>
            @endif

            @if(count($villain->npc->rules) >0)
                <div class="panel panel-default" >
                    <div class="panel-heading clearfix">
                        <h1 class="panel-title pull-left" style="padding-top: 7.5px;">Settlements Ruled</h1>
                        <button class="btn btn-primary pull-right" ng-click="showSettlements = !showSettlements"><% (showSettlements) ? 'Hide' : 'Show' %></button>
                    </div>
                    <div class="panel-body" ng-show="showSettlements">
                        @foreach($villain->npc->rules as $settlement)
                            @include('settlements.settlement_display', ['settlement'=>$settlement])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@section('scripts')
    <script>
        app.controller("VillainShowController", ['$scope', "$controller", function($scope, $controller) {
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            $scope.showVillain = true;

        }]);
    </script>
@stop


