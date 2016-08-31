<?php
$data = ['field' => $field, 'type' =>'number', 'model' => isset($model) ? $model : $headers->dataDefaults->model];

if(isset($validation)){
    $data['validation'] = $validation;
}

?>
@include('tiles.questions.input', $data)