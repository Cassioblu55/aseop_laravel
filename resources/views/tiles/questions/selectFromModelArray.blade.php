<?php
$model = (isset($model)) ? $model : $headers->dataDefaults->model;

$required = (isset($required) && $required== true);
$isRequired = $required ? 'required ="required"' : '';
?>

<div class="form-group {{ $errors->has($field) ? 'has-error' : '' }}">
    <label class="control-label" for="{{$field}}_inputDiv">{{\App\Services\StringUtils::display($field)}}</label>
    <select id="{{$field}}_inputDiv" {{$isRequired}} class="form-control" name="{{$field}}" ng-model="{{$model}}.{{$field}}">
        <option {{$required ? 'disabled' : ''}} value="">{{($required) ? 'Choose One' : 'Any'}}</option>

        <option ng-repeat="row in {{$modelData}}" value="<% row %>">
            <% getStringDisplay(row) %>
        </option>

    </select>

    @include('tiles.error', ['errorName' => $field])
</div>