@extends('layout.form', ['controller'=> false])

@section('form_title', 'Upload Villains')
@section('additional_form_params', 'enctype="multipart/form-data"')

@section('form_body')

    <label>File</label>
    <input type="file" required="required" name="fileToUpload" id="fileToUpload">

@stop