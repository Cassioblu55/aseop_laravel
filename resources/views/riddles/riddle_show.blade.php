@extends('layout.show')

@section('controller', 'RiddleShowController')

@section('show_title', $riddle->name)

@section('show_body')
    <h4>{{$riddle->riddle}}</h4>

    <div class="row">
        @if($riddle->hint)
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        <div class="panel-title pull-left">Hint</div>
                        <button class="btn btn-primary btn-sm pull-right" type="button"
                                ng-click="showHint = !showHint"><%(showHint) ? "Hide" :
                            "Show"%></button>
                    </div>
                    <div class="panel-body" ng-show="showHint">{{$riddle->hint}}</div>
                </div>
            </div>
        @endif
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <div class="panel-title pull-left">Solution</div>
                    <button class="btn btn-primary btn-sm pull-right" type="button"
                            ng-click="showSolution = !showSolution"><%(showSolution) ?
                        "Hide" : "Show"%></button>
                </div>
                <div class="panel-body" ng-show="showSolution">{{$riddle->solution}}</div>
            </div>
        </div>

    </div>
    <div class="col-md-12" ng-show="riddle.other_information">
        <h4>Other Information</h4>
        <div class="showDisplay">{{$riddle->other_information}}</div>
    </div>
@stop


@section('scripts')
<script>
    app.controller("RiddleShowController", ['$scope', "$controller", function($scope, $controller){
        angular.extend(this, $controller('UtilsController', {$scope: $scope}));



    }]);

</script>

@stop
