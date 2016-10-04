@extends('layout.app')

<?php
        $model = (isset($model)) ? $model : (isset($headers)) ? $headers->dataDefaults->model : '';
        $pural = $model."s";
?>


@section('content')
    <div ng-controller="@yield("controller", $headers->dataDefaults->indexController)">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                            <div class="panel-heading clearfix">
                                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">@yield("panelTitle")</h4>
                                @yield("additionalHeaderContent")
                            </div>
                            <div class="panel-body">
                                @yield("panelBody")
                            </div>
                            <div class="panel-footer">
                                @yield("panelFooter")
                                @if(!isset($noDownload) || isset($noDownload) && $noDownload == false)
                                    <a class="btn btn-default pull-right" href="{{url("/api/$pural/download/$pural.csv")}}">Download</a>
                                @endif

                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop