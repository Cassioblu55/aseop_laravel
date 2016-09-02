@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% settlement.name || 'Settlement' %>")

@section('controller', "SettlementsAddEditController")

@section('form-size', '6')

@section('form_body')
    @include("tiles.questions.text", ['field' =>'name', 'required' => true])

    @include("tiles.questions.textArea", ['field' =>'known_for'])
    @include("tiles.questions.textArea", ['field' =>'notable_traits'])
    @include("tiles.questions.textArea", ['field' =>'ruler_status'])
    @include("tiles.questions.textArea", ['field' =>'current_calamity'])

    @include("tiles.questions.number", ['field' =>'population', 'validation'=>'min=0'])

    @include('tiles.questions.select', ['field' => 'size', 'required' => true,'data' => ['S' => 'Small', 'M' => 'Medium' , 'L' => 'Large']])

    @include('tiles.questions.selectFromRemote', ['field' =>'ruler_id', 'remoteField'=> 'npcs' ,'remoteDisplayValue' => 'name'])

    @include("tiles.questions.text", ['field' =>'race_relations'])


    @include("tiles.questions.textArea", ['field' =>'other_information'])


    @include('tiles.questions.publicPrivate')
@stop

@section('back_location', url('/settlements'))

@section('scripts')
    <script>
        app.controller("SettlementsAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));


            const CONFIG = {localResource: 'settlements', defaultCheckObjectPresent: "{{$settlement->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(settlement){
                $scope.settlement = settlement;
            });

            $scope.utils.runOnCreate(function(){
                $scope.settlement = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.settlement['public'] = n});
            });

            const NPC_NAME_RESOURCE_URL = '{{url("/api/npcs/names")}}';
            $scope.setFromGet(NPC_NAME_RESOURCE_URL, function(data){
               $scope.npcs = data;
            });

        }]);
    </script>
@stop