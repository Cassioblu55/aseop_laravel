@extends('layout.grid')

@section('panelTitle', 'My ForestEncounter')

@section('controller', 'ForestEncounterIndexController')

@section('additionalHeaderContent')
    <a class="btn btn-default pull-right" style="margin-left: 5px" href="{{url('/forestEncounters/upload')}}">Upload</a>
    <a class="btn btn-primary pull-right" href="{{url('/forestEncounters/create')}}">Add</a>
@stop

@section('panelBody')
    <div ui-grid="gridModel" external-scopes="$scope" style="height: 400px;"></div>
@stop()

@section('panelFooter')
    <a class="btn btn-default" href="{{url('/')}}">Back</a>
@stop

@section('scripts')
    <script>
        app.controller("ForestEncounterIndexController", ['$scope', "$controller", function($scope, $controller) {

            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'forestEncounters', runOnGridRefresh: function(){
                $scope.gridModel.formatCreatedAt();
                $scope.gridModel.formatPublicPrivate();
            }};

            $scope.gridModel = $scope.ExtendedGrid(CONFIG);

            $scope.gridModel.setColumnDefs([
                {field: 'title'},
                {field: 'public'},
                {field: 'created_at', cellFilter: "date:'MM-dd-yy'", displayName: "Created"}
            ]);

            $scope.gridModel.refresh();

        }]);
    </script>
@stop()
