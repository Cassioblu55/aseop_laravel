<?php

    $required = (isset($required) && $required == true);
    $validation = (isset($validation)) ? $validation : '';
?>

<div class="form-group {{ $errors->has($field) ? 'has-error' : '' }}">
    <label class="control-label" for="{{$field}}_inputDiv">{{\App\Services\StringUtils::display($field)}}</label>
    <textarea name="{{$field}}" id="{{$field}}_inputDiv" ng-model="{{isset($model) ?  $model : $headers->dataDefaults->model}}.{{$field}}" placeholder="{{\App\Services\StringUtils::display($field)}}" class="form-control" rows="{{isSet($rows) ? $rows : 4 }}" {{$validation}} {{($required) ? 'required = "required"' : ''}}></textarea>
    @include('tiles.error', ['errorName' => $field])
</div>