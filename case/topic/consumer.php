<?php

$level = dirname(__DIR__, 2);
require_once $level . '/config.php';
require_once $level . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection($rmqHost, $rmqPort, $rmqLogin, $rmqPassword);
$channel = $connection->channel();

$channel->exchange_declare('topic_logs', 'topic', false, false, false);

list($queue_name, , ) = $channel->queue_declare("", false, false, true, false);

$argv[0] = 'consumer.php';
$argv[1] = '*.rabbit';
$argv[2] = 'red.rabbit Hello';

$binding_keys = array_slice($argv, 1);

if (empty($binding_keys)) {
    file_put_contents('php://stderr', "Don't forget to use: $argv[0] [binding_key]\n");
    exit(1);
}

foreach ($binding_keys as $binding_key) {
    $channel->queue_bind($queue_name, 'topic_logs', $binding_key);
}

echo " [*] Waiting Topic tasks...\n";

$callback = function ($msg) use ($level) {
    loadSimulation();
    echo ' [x] Topic Received ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
    $data = time().' '.$msg->delivery_info['routing_key'].' '.$msg->body."\n";
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
