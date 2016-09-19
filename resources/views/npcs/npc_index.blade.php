@extends('layout.grid')

@section('panelTitle', 'My Non Player Characters')

@section('controller', 'NpcIndexController')

@section('additionalHeaderContent')
    <a class="btn btn-default pull-right" style="margin-left: 5px" href="{{url('/npcs/upload')}}">Upload</a>
    <a class="btn btn-primary pull-right" href="{{url('/npcs/create')}}">Add</a>
@stop

@section('panelBody')
    <div ui-grid="gridModel" external-scopes="$scope" style="height: 400px;"></div>
@stop()

@section('panelFooter')
    <a class="btn btn-default" href="{{url('/')}}">Back</a>
@stop

@section('scripts')
    <script>
        app.controller("NpcIndexController", ['$scope', "$controller", function($scope, $controller) {

            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'npcs', runOnGridRefresh: function(){
                $scope.gridModel.formatColumnWithHash('sex', {'M' : 'Male', 'F' : 'Female'}, 'Other');
                $scope.gridModel.formatCreatedAt();
                $scope.gridModel.formatPublicPrivate();
            }};

            $scope.gridModel = $scope.ExtendedGrid(CONFIG);

            $scope.gridModel.setColumnDefs([
                {field: 'first_name'},
                {field: 'last_name'},
                {field: 'age'},
                {field: 'sex'},
                {field: 'appearance'},
                {field: 'public'},
                {field: 'created_at', cellFilter: "date:'MM-dd-yy'", displayName: "Created"}
            ]);

            $scope.gridModel.refresh();

        }]);
    </script>
@stop()
