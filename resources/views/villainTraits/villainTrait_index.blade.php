@extends('layout.grid')

@section('panelTitle', 'My VillainTrait')

@section('controller', 'VillainTraitIndexController')

@section('additionalHeaderContent')
    <a class="btn btn-default pull-right" style="margin-left: 5px" href="{{url('/villainTraits/upload')}}">Upload</a>
    <a class="btn btn-primary pull-right" href="{{url('/villainTraits/create')}}">Add</a>
@stop

@section('panelBody')
    <div ui-grid="gridModel" external-scopes="$scope" style="height: 400px;"></div>
@stop()

@section('panelFooter')
    <a class="btn btn-default" href="{{url('/')}}">Back</a>
@stop

@section('scripts')
    <script>
        app.controller("VillainTraitIndexController", ['$scope', "$controller", function($scope, $controller) {

            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'villainTraits', runOnGridRefresh: function(){
                $scope.gridModel.formatCreatedAt();
                $scope.gridModel.formatPublicPrivate();
            }};

            $scope.gridModel = $scope.ExtendedGrid(CONFIG);

            $scope.gridModel.setColumnDefs([
                {field: 'type'},
                {field: 'kind'},
                {field: 'description'},
                {field: 'public'},
                {field: 'created_at', cellFilter: "date:'MM-dd-yy'", displayName: "Created"}
            ]);

            $scope.gridModel.refresh();

        }]);
    </script>
@stop()
