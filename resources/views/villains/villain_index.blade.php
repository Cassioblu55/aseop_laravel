@extends('layout.grid')

@section('panelTitle', 'My Villain')

@section('controller', 'VillainIndexController')

@section('additionalHeaderContent')
    <a class="btn btn-default pull-right" style="margin-left: 5px" href="{{url('/villains/upload')}}">Upload</a>
    <a class="btn btn-primary pull-right" href="{{url('/villains/create')}}">Add</a>
@stop

@section('panelBody')
    <div ui-grid="gridModel" external-scopes="$scope" style="height: 400px;"></div>
@stop()

@section('panelFooter')
    <a class="btn btn-default" href="{{url('/')}}">Back</a>
@stop

@section('scripts')
    <script>
        app.controller("VillainIndexController", ['$scope', "$controller", function($scope, $controller) {

            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'villains', runOnGridRefresh: function(){
                $scope.gridModel.formatCreatedAt();
                $scope.gridModel.formatPublicPrivate();

                const NPC_NAME_RESOURCE_URL = '{{url("/api/npcs/names")}}';
                $scope.setFromGet(NPC_NAME_RESOURCE_URL, function(nameData){
                    angular.forEach($scope.gridModel.data, function(gridRow){
                        angular.forEach(nameData, function(nameRow){
                            if(gridRow.npc_id == nameRow.id){
                                gridRow.name = nameRow.name;
                            }
                        })
                    });
                });
            }};

            $scope.gridModel = $scope.ExtendedGrid(CONFIG);

            $scope.gridModel.setColumnDefs([
                {field: 'name'},
                {field: 'method_type'},
                {field: 'scheme_type'},
                {field: 'weakness_type'},
                {field: 'public'},
                {field: 'created_at', cellFilter: "date:'MM-dd-yy'", displayName: "Created"}
            ]);

            $scope.gridModel.refresh();

        }]);
    </script>
@stop()
