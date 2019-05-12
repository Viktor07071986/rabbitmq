<?php
namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Reader implements Base {

    public $connection;
    public $channel;

    function __construct() {
        $this->connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
    }

    function render() {
        echo "<form action=".$_SERVER["REQUEST_URI"]." method='POST' style='margin-top: 25px;'>
            Сколько сообщений выгрузить из очереди RabbitMQ?<br/>
            <input type='text' name='count_queue_message' required placeholder='Выберите значение больше 0' size='25' value='".$_POST["count_queue_message"]."'><br/><br/>
            <input type='submit' value='Выгрузить'/>
        </form>";
    }

    function processData() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo "<table border='1px solid black;' cellspacing='0' cellpadding='7'>
                    <tr>
                        <th>Логин</th>
                        <th>Заголовок сообщения</th>
                        <th>Сообщение</th>
                        <th>Время</th>
                    </tr>";
        }
        for ($i = 0; $i < $_POST["count_queue_message"]; $i++) {
            $result = ($this->channel->basic_get('RabbitMQQueue', true, null)->body);
            $rez = json_decode($result, true);
            if (is_null($rez)) {
                echo "<hr/>Доступных сообщений для выгрузки больше нет!";
                break;
            } else {
                echo "<tr>
                    <td>".$rez["firstname"]."</td>
                    <td>".$rez["header_message"]."</td>
                    <td>".nl2br($rez["text_message"])."</td>
                    <td>".$rez["date_message"]."</td>
                </tr>";
            }
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo "</table>";
        }
    }

    function __destruct() {
        $this->channel->close();
        $this->connection->close();
    }

}

?>