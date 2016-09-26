@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% tavern.name || 'Tavern' %>")

@section('controller', "TavernAddEditController")

@section('form-size', '6')

@section('form_body')
    @include("tiles.questions.text", ['field' =>'name'])

    @include("tiles.questions.text", ['field' =>'type'])

    @include("tiles.questions.selectWithModelOptions", ['field' => 'tavern_owner_id', 'required' => true, 'remoteField'=>'npcs', 'remoteDisplayValue' => 'name'])

    @include("tiles.questions.textArea", ['field' =>'other_information'])

    @include('tiles.questions.publicPrivate')
@stop

@section('back_location', url('/taverns'))

@section('scripts')
    <script>
        app.controller("TavernAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'taverns', defaultCheckObjectPresent: "{{$tavern->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(tavern){
                $scope.tavern = tavern;
                $scope.tavern.tavern_owner_id = $scope.tavern.tavern_owner_id+'';
            });

            $scope.utils.runOnCreate(function(){
                $scope.tavern = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.tavern['public'] = n});
            });

            const NPC_NAME_RESOURCE_URL = '{{url("/api/npcs/names")}}';
            $scope.setFromGet(NPC_NAME_RESOURCE_URL, function(data){
                $scope.npcs = data;
            });

        }]);
    </script>
@stop