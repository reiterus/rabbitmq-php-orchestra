<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection($rmqHost, $rmqPort, $rmqLogin, $rmqPassword);
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);
$data = uniqid(). " > Just Said \"Hello\"... from ".__FILE__;

$msg = new AMQPMessage($data);
$channel->basic_publish($msg, '', 'hello');

echo ' [x] Sent Hello ', $data, "\n";

$channel->close();
$connection->close();
