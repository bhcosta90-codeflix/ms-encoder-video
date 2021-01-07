<?php

include __DIR__ . '/../bootstrap.php';

$rabbit = new Rabbitmq();
$rabbit->consume("ms-encoder/video/upload", function($msg){
    $data = json_decode($msg->body);
    $obj = new \Video\Upload($data->filename, $data->field);
    $obj->upload((array) $data->files);
}, "amq.topic", "ms-encoder.video.upload");