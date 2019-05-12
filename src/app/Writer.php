<?php
namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class Writer implements Base {

    private $queue;
    private $exchange;
    private $connection;
    private $channel;

    function __construct() {
        $this->queue = "RabbitMQQueue";
        $this->exchange = "amq.direct";
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest', '/', false, 'AMQPLAIN', null, 'en_US', 30);
        $this->channel = $this->connection->channel();
    }

    function render() {
        return "<form action=".$_SERVER["REQUEST_URI"]." method='POST' style='margin-top: 25px;'>
            Логин:<br/>
            <input type='text' name='firstname' required value='".$_POST["firstname"]."'><br/>
            Заголовок сообщения:<br/>
            <input type='text' name='header_message' required value='".$_POST["header_message"]."'><br/>
            Сообщение:<br/>
            <textarea rows='10' cols='45' name='text_message' required></textarea><br/>
            <input type='hidden' name='date_message' value='".date("d/m/Y H:i:s")."'><br/><br/>
            <input type='submit' value='Отправить'/>
        </form>";
    }

    function processData() {
        $this->channel->queue_declare($this->queue, false, true, false, false);
        $this->channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, true, false);
        $this->channel->queue_bind($this->queue, $this->exchange);
        $messageBody = json_encode($_POST);
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $this->channel->basic_publish($message, $this->exchange);
    }

    function __destruct() {
        $this->channel->close();
        $this->connection->close();
    }

}

?>