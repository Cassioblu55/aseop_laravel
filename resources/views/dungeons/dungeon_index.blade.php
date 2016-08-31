@extends('layout.grid')

@section('panelTitle', 'My Dungeons')

@section('controller', 'DungeonIndexController')

@section('additionalHeaderContent')
    <a class="btn btn-primary pull-right" href="{{url('/dungeons/create')}}">Add</a>
@stop

@section('panelBody')
    <div ui-grid="gridModel" external-scopes="$scope" style="height: 400px;"></div>
@stop()

@section('panelFooter')
    <a class="btn btn-default" href="{{url('/')}}">Back</a>
@stop

@section('scripts')
    <script>
        app.controller("DungeonIndexController", ['$scope', "$controller","$http", function($scope, $controller, $http) {

            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'dungeons', runOnGridRefresh: function(){
                $scope.gridModel.formatCreatedAt();
                $scope.gridModel.formatPublicPrivate();
                $scope.gridModel.formatColumnWithHash('size', {"M":"Medium", "S": "Small", "L" : "Large"});
            }};

            $scope.gridModel = $scope.ExtendedGrid(CONFIG);

            $scope.gridModel.setColumnDefs([
                {field: 'name'},
                {field: 'purpose'},
                {field: 'size'},
                {field: 'public'},
                {field: 'created_at', cellFilter: "date:'MM-dd-yy'", displayName: "Created"}
            ]);

            $scope.gridModel.refresh();

        }]);
    </script>
@stop()
