<?php
    $model = isset($model) ? $model : $headers->dataDefaults->model;
?>

<div class="form-group">
    <label class="control-label" for="public_inputDiv" >Public or Private</label>
    <select class="form-control" id="public_inputDiv" name="public" ng-model="{{$model}}.public">
        <option ng-selected="{{$model}}.public=='1'" value="1">Public</option>
        <option  ng-selected="{{$model}}.public=='0'" value="0">Private</option>
    </select>
</div>