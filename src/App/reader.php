<?php
namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class Reader implements Base {
    function render() {
        $queue = "ololo";
        $exchange = "amq.direct";

        $connection = new AMQPStreamConnection(
                '10.19.7.5',
                5672,
                'rabbitmq',
                'rabbitmq',
                '/', false, 'AMQPLAIN', null, 'en_US', 30
            );
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare("amq.direct", AMQPExchangeType::DIRECT, false, true, false);

        $channel->queue_bind($queue, $exchange);
        $messageBody = "ololololololololololololololo";
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange);
        $channel->close();
        $connection->close();
        echo "Hello, I'am render from READER";
    }
    function processData() {
        echo "Hello, I'am processData from READER";
    }
}