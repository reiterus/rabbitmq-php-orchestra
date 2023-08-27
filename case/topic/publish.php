<?php

$level = dirname(__DIR__, 2);
require_once $level . '/config.php';
require_once $level . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection($rmqHost, $rmqPort, $rmqLogin, $rmqPassword);
$channel = $connection->channel();

$channel->exchange_declare('topic_logs', 'topic', false, false, false);

$routing_key = !empty($argv[1]) ? $argv[1] : 'red.rabbit';

$data = implode(' ', array_slice($argv, 2));

if (empty($data)) {
    $data = ' > '.uniqid(). " > Topic Exchange Data from publisher";
}

$msg = new AMQPMessage($data);

$channel->basic_publish($msg, 'topic_logs', $routing_key);

echo ' [x] Sent Topic ', $routing_key, ': ', $data, "\n";

$channel->close();
$connection->close();
