<?php

    $required = (isset($required) && $required == true);
    $validation = (isset($validation)) ? $validation : '';
?>


<div class="form-group {{ $errors->has($field) ? 'has-error' : '' }}">
    <label class="control-label" for="{{$field}}_inputDiv">{{\App\Services\StringUtils::display($field)}}</label>
    <input type="{{$type}}" id="{{$field}}_inputDiv" class="form-control" name="{{$field}}" ng-model="{{isset($model) ? $model : $headers->dataDefaults->model}}.{{$field}}" placeholder="{{\App\Services\StringUtils::display($field)}}" {{$validation}} {{($required) ? 'required = "required"' : ''}} />
    @include('tiles.error', ['errorName' => $field])
</div>