<?php
    $model = isset($model) ? $model : $headers->dataDefaults->model;
?>

@include('tiles.questions.select', ['model'=> $model, 'field' => 'public', 'data' => [
    1 => 'Public' , 0 => 'Private'
]])