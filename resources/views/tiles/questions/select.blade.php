<?php
    $model = (isset($model)) ? $model : $headers->dataDefaults->model;
?>

<div class="form-group {{ $errors->has($field) ? 'has-error' : '' }}">
   <label class="control-label" for="{{$field}}_inputDiv">{{\App\Services\StringUtils::display($field)}}</label>
        <select id="{{$field}}_inputDiv" class="form-control" name="{{$field}}" ng-model="{{$model}}.{{$field}}">
        <option value="">Any</option>

        @foreach($data as $value=>$displayValue)
            <option ng-selected="{{$model}}.{{$field}}=='{{$value}}'" value="{{$value}}">{{$displayValue}}</option>
        @endforeach

    </select>
</div>