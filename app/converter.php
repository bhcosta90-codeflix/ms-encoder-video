<?php

include __DIR__ . '/../bootstrap.php';

use Video\{
    Download,
    Fragment,
    Converter,
    Upload
};

$rabbit = new Rabbitmq();
$rabbit->consume("ms-encoder/video/converter", function($msg){
    $data = json_decode($msg->body);
    (new Download($data->file, $data->field))->exec();
    (new Fragment($data->file, $data->field))->exec();
    (new Converter($data->file, $data->field))->exec();
    (new Upload($data->file, $data->field))->exec();
}, "amq.topic", "ms-encoder.video.converter");
//

//

/*use Google\Cloud\Storage\StorageClient;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Google\Cloud\Core\ServiceBuilder;

$cloud = new ServiceBuilder([
    'keyFilePath' => 'codeflix-298009-58b7e01f8396.json'
]);

$storage = new StorageClient([
    'keyFile' => json_decode(file_get_contents(__DIR__ . '/codeflix-298009-58b7e01f8396.json'), true),
]);
$storage->registerStreamWrapper();
$contents = file_get_contents('gs://codemicrovideos/videoplayback.mp4');
print $contents;

//$connection = new AMQPStreamConnection('localhost', 5672, 'root', 'root', 'rabbitmq');
//$channel = $connection->channel();
//
//$channel->queue_declare('ms-encoder/video/converter', false, false, false, false);
//
//$callback = function ($msg) {
//    echo ' [x] Received ', $msg->body, "\n";
//};
//
//$channel->basic_consume('ms-encoder/video/converter', '', false, true, false, false, $callback);
//
//while ($channel->is_consuming()) {
//    $channel->wait();
//}*/
