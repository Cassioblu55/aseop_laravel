@extends('layout.form')

@section('form_title', 'Upload Settlement Traits')
@section('additional_form_params', 'enctype="multipart/form-data"')

@section('form_body')

    <label>File</label>
    <input type="file" name="fileToUpload" id="fileToUpload">

@stop