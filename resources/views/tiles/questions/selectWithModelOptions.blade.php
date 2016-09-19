<?php
//Uses $field, $remoteDisplayValue, $remoteIdentifer,

$model = (isset($model)) ? $model : $headers->dataDefaults->model;

$displayName = str_replace("_id", "", $field);
$required = (isset($required) && $required== true);
$isRequired = $required ? 'required ="required"' : '';
$modelField = $model.".".$field;

$remoteIdentifer = (isset($remoteIdentifer)) ? $remoteIdentifer : 'id'

?>

<div class="form-group {{ $errors->has($field) ? 'has-error' : '' }}">
    <label class="control-label" for="{{$field}}_inputDiv">{{\App\Services\StringUtils::display($displayName)}}</label>
    <select id="{{$field}}_inputDiv" {{$isRequired}} class="form-control" name="{{$field}}" ng-model="{{$modelField}}">
        <option {{$required ? 'disabled' : ''}} value="">{{($required) ? 'Choose One' : 'Any'}}</option>

        <option ng-repeat="remoteRow in {{$remoteField}}" value="<% remoteRow.{{$remoteIdentifer}} %>" ng-selected="{{$modelField}} == remoteRow.{{$remoteIdentifer}}"><%remoteRow.{{$remoteDisplayValue}}%></option>
    </select>

    @include('tiles.error', ['errorName' => $field])
</div>