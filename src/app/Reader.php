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
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        for ($i = 0; $i < $_POST["count_queue_message"]; $i++) {
            $result = ($channel->basic_get('RabbitMQQueue', true, null)->body);
            $rez = json_decode($result, true);
            if (is_null($rez)) {
                echo "<hr/>Доступных сообщений для выгрузки больше нет!";
                break;
            } else {
                echo "<hr/>" . $rez["firstname"] . " " . $rez["header_message"] . " " . $rez["text_message"] . " " . $rez["date_message"];
            }
        }
        $channel->close();
        $connection->close();
    }

}

?>