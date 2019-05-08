<?php
namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class Writer implements Base {

    function render() {
        echo "<form action=".$_SERVER["REQUEST_URI"]." method='POST' style='margin-top: 25px;'>
            Логин:<br/>
            <input type='text' name='firstname' required value='".$_POST["firstname"]."'><br/>
            Заголовок сообщения:<br/>
            <input type='text' name='header_message' required value='".$_POST["header_message"]."'><br/>
            Сообщение:<br/>
            <textarea rows='10' cols='45' name='text_message' required></textarea><br/>
            Дата:<br/>
            <input type='text' name='date_message' value='".date("d/m/Y H:i:s")."' readonly><br/><br/>
            <input type='submit' value='Отправить'/>
        </form>";
    }

    function processData() {
        $queue = "RabbitMQQueue";
        $exchange = "amq.direct";
        $connection = new AMQPStreamConnection(
            'localhost',
            5672,
            'guest',
            'guest',
            '/', false, 'AMQPLAIN', null, 'en_US', 30
        );
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare("amq.direct", AMQPExchangeType::DIRECT, false, true, false);
        $channel->queue_bind($queue, $exchange);
        $messageBody = json_encode($_POST);
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange);
        $channel->close();
        $connection->close();
    }

}

?>