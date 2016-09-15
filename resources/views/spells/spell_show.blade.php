@extends('layout.show')

@section('show_title', $spell->name)

@section('show_body')
    <div class="container-fluid">
        <strong class="row bold">
            {{$spell->level}} {{$spell->class}} {{$spell->casting_time}} {{$spell->range}} {{$spell->components}} {{$spell->duration}}
        </strong>

        <div class="showDisplay">
            {{$spell->description}}
        </div>

    </div>


@stop

@section('scripts')
    <script>
        app.controller("SpellShowController", ['$scope', "$controller", "$window" , function($scope, $controller) {
            angular.extend(this, $controller('UtilsController', {$scope: $scope}));

        }]);

    </script>

@stop