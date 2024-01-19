<?php

$level = dirname(__DIR__, 2);
require_once $level . '/config.php';
require_once $level . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection($rmqHost, $rmqPort, $rmqLogin, $rmqPassword);
$channel = $connection->channel();

$channel->exchange_declare('rpo_topic', 'topic', false, false, true);

list($queue_name, , ) = $channel->queue_declare("", false, false, true, true);

$binding_keys = ['*.rabbit'];
foreach ($binding_keys as $binding_key) {
    $channel->queue_bind($queue_name, 'rpo_topic', $binding_key);
}

echo " [*] Waiting Topic tasks...\n";

$callback = function ($msg) use ($level) {
    loadSimulation();
    echo ' [x] Topic Received ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
    $data = $msg->delivery_info['routing_key'].' '.$msg->body."\n";
    $fnm = $level.'/'.'case_topic_'.date('Y-m-d_H-i-s').'.txt';
    file_put_contents($fnm, $data);
    changeRights($fnm);
};

$channel->basic_consume($queue_name, '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
