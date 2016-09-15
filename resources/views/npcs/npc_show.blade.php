@extends('layout.show')

@section('show_title', $npc->displayName())

@section('show_body')
    @include('npcs.npc_display', ['npc' => $npc, 'hide' => true, 'title' => "Stats"])

    @if($npc->villainous != null)
        @include('villains.villain_display', ['villain'=>$npc->villainous, 'hide'=>true])
    @endif

    @if(count($npc->owns) >0)
        <div class="panel panel-default" >
            <div class="panel-heading clearfix">
                <h1 class="panel-title pull-left" style="padding-top: 7.5px;">Taverns Owned</h1>
                <button class="btn btn-primary pull-right" ng-click="showTaverns = !showTaverns"><% (showTaverns) ? 'Hide' : 'Show' %></button>
            </div>
            <div class="panel-body" ng-show="showTaverns">
                @foreach($npc->owns as $tavern)
                    @include('taverns.tavern_display', ['tavern'=>$tavern])
                @endforeach
            </div>
        </div>
    @endif

    @if(count($npc->rules) >0)
        <div class="panel panel-default" >
            <div class="panel-heading clearfix">
                <h1 class="panel-title pull-left" style="padding-top: 7.5px;">Settlements Ruled</h1>
                <button class="btn btn-primary pull-right" ng-click="showSettlements = !showSettlements"><% (showSettlements) ? 'Hide' : 'Show' %></button>
            </div>
            <div class="panel-body" ng-show="showSettlements">
                @foreach($npc->rules as $settlement)
                    @include('settlements.settlement_display', ['settlement'=>$settlement])
                @endforeach
            </div>
        </div>
    @endif

@stop







@section('scripts')
    <script>
        app.controller("NpcShowController", ['$scope', "$controller", function($scope, $controller) {
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            $scope.showNpc = true;

        }]);
    </script>
@stop


