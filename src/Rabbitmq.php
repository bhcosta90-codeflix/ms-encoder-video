<?php


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Rabbitmq
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(){
        $this->reconnect();
    }

    private function reconnect()
    {
        $this->connection = new AMQPStreamConnection('codeflix_rabbitmq', 5672, 'root', 'root', 'rabbitmq');
        $this->channel = $this->connection->channel();
    }

    public function publish($message, $routingKey, $exchange = "amq.topic")
    {
        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, $exchange, $routingKey);
    }

    public function consume($queue, $callback, $exchange_name=null, $binding_key=null){
        try{
            $this->channel->basic_consume($queue, '', false, true, false, false, $callback);

            while ($this->channel->is_consuming()) {
                $this->channel->wait();
            }
        }catch(\PhpAmqpLib\Exception\AMQPProtocolChannelException $e){
            if(strpos($e->getMessage(), "NOT_FOUND - no queue") !== null){
                $this->reconnect();
                $this->channel->queue_declare($queue, false, false, false, false);
                if($exchange_name && $binding_key){
                    $this->channel->queue_bind($queue, $exchange_name, $binding_key);
                }
                $this->consume($queue, $callback);
            }
        }
    }
}