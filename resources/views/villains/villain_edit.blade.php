@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% displayName %>")

@section('controller', "VillainAddEditController")

@section('form-size', '6')

@section('form_body')

    @include('tiles.questions.selectFromRemote', ['field' => 'npc_id', 'remoteField'=>'npcs', 'remoteDisplayValue' => 'name'])

    @include('tiles.questions.text', ['field' => 'method_type'])
    @include('tiles.questions.textArea', ['field' => 'method_description'])

    @include('tiles.questions.text', ['field' => 'scheme_type'])
    @include('tiles.questions.textArea', ['field' => 'scheme_description'])

    @include('tiles.questions.text', ['field' => 'weakness_type'])
    @include('tiles.questions.textArea', ['field' => 'weakness_description'])

    @include('tiles.questions.textArea', ['field' => 'other_information'])

    @include('tiles.questions.publicPrivate')
@stop

@section('back_location', url('/villains'))

@section('scripts')
    <script>
        app.controller("VillainAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'villains', defaultCheckObjectPresent: "{{$villain->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(villain){
                $scope.villain = villain;
                $scope.villain.npc_id = $scope.villain.npc_id+"";
            });

            $scope.utils.runOnCreate(function(){
                $scope.villain = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.villain['public'] = n});
            });

            $scope.$watch('villain.npc_id', function(val){
                $scope.displayName = "Villain";
                setDisplayName(val);
            });

            function setDisplayName(id){
                if(id){
                    angular.forEach($scope.npcs, function(row){
                        if(row && row.id == id){
                            $scope.displayName =  row.name+", Villain";
                        }
                    })
                }
            }

            const NPC_NAME_RESOURCE_URL = '{{url("/api/npcs/names")}}';
            $scope.setFromGet(NPC_NAME_RESOURCE_URL, function(data){
                $scope.npcs = data;
                if($scope.villain){setDisplayName($scope.villain.npc_id);}
            });

        }]);
    </script>
@stop