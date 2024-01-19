<?php

$level = dirname(__DIR__, 2);
require_once $level . '/config.php';
require_once $level . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection($rmqHost, $rmqPort, $rmqLogin, $rmqPassword);
$channel = $connection->channel();

$channel->exchange_declare('rpo_topic', 'topic', false, false, true);

$routing_key = 'red.rabbit';
$data = uniqid(). " > Topic Exchange Data from ".__FILE__;

$msg = new AMQPMessage($data);
$channel->basic_publish($msg, 'rpo_topic', $routing_key);

echo ' [x] Sent Topic ', $routing_key, ': ', $data, "\n";

$channel->close();
$connection->close();
