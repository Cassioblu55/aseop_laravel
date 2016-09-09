@extends('layout.show')

@section('controller', 'Base_nameController')

@section('show-size', '8')

@section('show_title', (isset($base_name->name)) ? $base_name->name : 'Base_name')

@section('show_body')

@stop

@section('edit_link', url("/base_names/$base_name->id/edit"))

@section('scripts')
    <script>
        app.controller("Base_nameController", ['$scope', "$controller", function($scope, $controller) {
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

        }]);
    </script>
@stop