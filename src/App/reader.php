<?php
namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Reader implements Base {

    function render() {
        echo "<form action=".$_SERVER["REQUEST_URI"]." method='POST' style='margin-top: 25px;'>
            Сколько сообщений выгрузить из очереди RabbitMQ?<br/>
            <input type='text' name='count_queue_message' required placeholder='Выберите значение больше 0' size='25' value='".$_POST["count_queue_message"]."'><br/><br/>
            <input type='submit' value='Выгрузить'/>
        </form>";
    }

    function processData() {
        //echo "Hello, I'am processData from READER";
        /*$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $result = ($channel->basic_get('RabbitMQQueue', true, null)->body);
        //var_dump($result);
        $channel->close();
        $connection->close();*/

        $connection = new AMQPStreamConnection(
            'localhost',
            5672,
            'guest',
            'guest'
        );
        $channel = $connection->channel();
        $channel->queue_declare(
            'RabbitMQQueue',
            false,
            true,
            false,
            false
            );
        $channel->basic_consume(
            'RabbitMQQueue',
            '',
            false,
            true,
            false,
            false,
            array($this, 'processOrder')
        );
        while(count($channel->callbacks)) {
            //$channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    public function processOrder($msg) {
        /* ... КОД ОБРАБОТКИ ЗАКАЗА ... */
        echo $msg;
    }


}

?>