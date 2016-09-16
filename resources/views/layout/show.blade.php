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
                            @if(!isset($noEdit) || $noEdit == false)
                                <a class="btn btn-default" href="@yield('edit_link', URL::current()."/edit")" type="submit">Edit</a>
                                @endif
                            @if(!isset($noDelete) || $noDelete == false)
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>
                            @endif
                            @yield('show_footer')
                        </div>
                    </div>
                </div>
            </div>
    </div>

    @if(!isset($noDelete) || $noDelete == false)
        <div id="deleteModal" class="modal fade" role="dialog">
            <form action="@yield('delete_location', URL::current())" method="POST" @yield('additional_form_params')>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                {{method_field("DELETE")}}
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Delete</h4>
                        </div>
                        <div class="modal-body">
                            @yield('delete_model_body', 'Are you sure you want to delete this object?')
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" type="submit">Delete</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif

@stop