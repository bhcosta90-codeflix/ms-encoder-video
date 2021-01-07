<?php

include __DIR__ . '/../bootstrap.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'root', 'root', 'rabbitmq');
$channel = $connection->channel();

$msg = new AMQPMessage('Hello World!');
$channel->basic_publish($msg, 'amq.topic', 'models.video.converted',);

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();

