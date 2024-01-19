<?php

$level = dirname(__DIR__, 2);
require_once $level . '/config.php';
require_once $level . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection($rmqHost, $rmqPort, $rmqLogin, $rmqPassword);
$channel = $connection->channel();

$channel->exchange_declare('rpo_fanout', 'fanout', false, false, true);
$data = uniqid(). " > Fanout Exchange Data from ".__FILE__;

$msg = new AMQPMessage($data);
$channel->basic_publish($msg, 'rpo_fanout');

echo ' [x] Sent Fanout: ', $data, "\n";

$channel->close();
$connection->close();
