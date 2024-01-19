<?php

$level = dirname(__DIR__, 2);
require_once $level . '/config.php';
require_once $level . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection($rmqHost, $rmqPort, $rmqLogin, $rmqPassword);
$channel = $connection->channel();

$channel->exchange_declare('rpo_direct', 'direct', false, false, true);

$severity = 'info';
$data = uniqid(). " > Direct Exchange Data from ".__FILE__;

$msg = new AMQPMessage($data);
$channel->basic_publish($msg, 'rpo_direct', $severity);

echo ' [x] Sent Direct ', $data, "\n";

$channel->close();
$connection->close();
