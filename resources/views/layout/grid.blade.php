@extends('layout.app')

@section('content')
    <div ng-controller="@yield("controller")">
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
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop