@extends('layout.form')

@section('form_title', "$headers->createOrUpdate <% base_name.name || 'Base_name' %>")

@section('controller', "Base_nameAddEditController")

@section('form-size', '6')

@section('form_body')
@stop

@section('back_location', url('/base_names'))

@section('scripts')
    <script>
        app.controller("Base_nameAddEditController", ['$scope', "$controller", function($scope, $controller){
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

            const CONFIG = {localResource: 'base_names', defaultCheckObjectPresent: "{{$base_name->id}}"};
            $scope.utils = $scope.CreateEditUtil(CONFIG);

            $scope.utils.getDataOnEdit(function(base_name){
                $scope.base_name = base_name;
            });

            $scope.utils.runOnCreate(function(){
                $scope.base_name = {};
                $scope.utils.getDefaultAccess(function(n){
                    $scope.base_name['public'] = n});
            });

        }]);
    </script>
@stop