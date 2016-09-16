@extends('layout.app')

@section('content')
    <div ng-controller="@yield('controller', $headers->dataDefaults->addEditController)">
        <form action="{{$headers->postLocation}}" method="POST" @yield('additional_form_params')>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            {{method_field($headers->methodField)}}

            <div class="container-fluid">
                <div class="col-md-@yield('form-size')">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h1 class="panel-title">@yield('form_title')</h1>
                        </div>
                        <div class="panel-body">
                            @yield('form_body')
                        </div>

                        <div class="panel-footer">
                            <button class="btn btn-primary" type="submit">{{$headers->addOrSave}}</button>
                            <a href="@yield('back_location', '.')" class="btn btn-default">Back</a>

                            @yield('form_footer')
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop