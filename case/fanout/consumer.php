<?php

$level = dirname(__DIR__, 2);
require_once $level . '/config.php';
require_once $level . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection($rmqHost, $rmqPort, $rmqLogin, $rmqPassword);
$channel = $connection->channel();

$channel->exchange_declare('rpo_fanout', 'fanout', false, false, false);

list($queue_name, , ) = $channel->queue_declare("", false, false, true, false);

$channel->queue_bind($queue_name, 'rpo_fanout');

echo " [*] Waiting Fanout tasks...\n";

$callback = function ($msg) use ($level) {
    loadSimulation();
    echo ' [x] Fanout Received ', $msg->body, "\n";
    $data = $msg->body."\n";
    $fnm = $level.'/'.'case_fanout_'.date('Y-m-d_H-i-s').'.txt';
    file_put_contents($fnm, $data);
    changeRights($fnm);
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
