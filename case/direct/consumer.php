<?php

$level = dirname(__DIR__, 2);
require_once $level . '/config.php';
require_once $level . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection($rmqHost, $rmqPort, $rmqLogin, $rmqPassword);
$channel = $connection->channel();

$channel->exchange_declare('rpo_direct', 'direct', false, false, true);

list($queue_name, , ) = $channel->queue_declare("", false, false, true, true);
$severities = ['info'];

foreach ($severities as $severity) {
    $channel->queue_bind($queue_name, 'rpo_direct', $severity);
}

echo " [*] Waiting Direct tasks...\n";

$callback = function ($msg) use ($level) {
    loadSimulation();
    echo ' [x] Direct Received ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
    $data = $msg->delivery_info['routing_key'].' '.$msg->body."\n";
    $fnm = $level.'/'.'case_direct_'.date('Y-m-d_H-i-s').'.txt';
    file_put_contents($fnm, $data);
    changeRights($fnm);
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
