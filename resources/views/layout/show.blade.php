@extends('layout.app')

@section('content')
    <div ng-controller="@yield('controller', $headers->dataDefaults->showController)">
            <div class="container-fluid">
                <div class="col-md-@yield('show-size')">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h1 class="panel-title">@yield('show_title')</h1>
                        </div>
                        <div class="panel-body">
                            @yield('show_body')
                        </div>

                        <div class="panel-footer">
                            <a href="@yield('back_location', '.')" class="btn btn-default">Back</a>
                            <a class="btn btn-default" href="@yield('edit_link', URL::current()."/edit")" type="submit">Edit</a>

                            @yield('show_footer')
                        </div>
                    </div>
                </div>
            </div>
    </div>
@stop