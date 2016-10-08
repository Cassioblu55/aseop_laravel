@extends('layout.grid')

@section('panelTitle', 'My Base_name')

@section('controller', 'Base_nameIndexController')

@section('additionalHeaderContent')
    <a class="btn btn-primary pull-right" href="{{url('/base_names/create')}}">Add</a>
@stop

@section('panelBody')
    <div ui-grid="gridModel" external-scopes="$scope" style="height: 400px;"></div>
@stop()

@section('panelFooter')
    <a class="btn btn-default" href="{{url('/')}}">Back</a>
@stop

@section('scripts')
    <script>
        app.controller("Base_nameIndexController", ['$scope', "$controller", function($scope, $controller) {

            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'base_names', runOnGridRefresh: function(){
                $scope.gridModel.formatCreatedAt();
                $scope.gridModel.formatPublicPrivate();
            }};

            $scope.gridModel = $scope.ExtendedGrid(CONFIG);

            $scope.gridModel.setColumnDefs([

            ]);

            $scope.gridModel.refresh();

        }]);
    </script>
@stop()
