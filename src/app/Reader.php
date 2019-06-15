<?php
namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Reader implements Base {

    private $connection;
    private $channel;
    //private $return;
    private $data = array();

    function __construct() {
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
    }

    function render() {
        return "<form action=".$_SERVER["REQUEST_URI"]." method='POST' style='margin-top: 25px;'>
            Сколько сообщений выгрузить из очереди RabbitMQ?<br/>
            <input type='text' name='count_queue_message' required placeholder='Выберите значение больше 0' size='25' value='".$_POST["count_queue_message"]."'><br/><br/>
            <input type='submit' value='Выгрузить'/>
        </form>";
    }

    function processData() {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            for ($i = 0; $i < $_POST["count_queue_message"]; $i++) {
                $result = ($this->channel->basic_get('RabbitMQQueue', true, null)->body);
                $rez = json_decode($result, true);
                if (is_null($rez)) {
                    echo "<p>Доступных сообщений для выгрузки больше нет!</p>";
                    break;
                } else {
                    $this->data[$i]=$rez;
                }
            }
            return $this->data;
        }

    }

    function __destruct() {
        $this->channel->close();
        $this->connection->close();
    }

}

?>