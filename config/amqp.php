<?php

return [

    'use' => 'production',

    'properties' => [

        'production' => [
            'host'                => env('RABBITMQ_HOST'),
            'port'                => env('RABBITMQ_PORT', 5672),
            'username'            => env('RABBITMQ_LOGIN', ''),
            'password'            => env('RABBITMQ_PASSWORD', ''),
            'vhost'               => env('RABBITMQ_VHOST', '/'),
            'exchange'            => env('RABBITMQ_EX', 'videos'),
            'exchange_type'       => env('RABBITMQ_TYPE', 'direct'),
            'consumer_tag'        => env('RABBITMQ_CONSUMER', 'consumer'),
            'ssl_options'         => [], // See https://secure.php.net/manual/en/context.ssl.php
            'connect_options'     => [], // See https://github.com/php-amqplib/php-amqplib/blob/master/PhpAmqpLib/Connection/AMQPSSLConnection.php
            'queue_properties'    => ['x-ha-policy' => ['S', 'all']],
            'exchange_properties' => [],
            'timeout'             => 0
        ],

    ],

];
