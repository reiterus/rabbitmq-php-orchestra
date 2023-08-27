<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection($rmqHost, $rmqPort, $rmqLogin, $rmqPassword);
$channel = $connection->channel();

$channel->queue_declare('hello', false, false, false, false);

echo " [*] Waiting hello tasks...\n";

$callback = function ($msg) {
    loadSimulation();
    echo ' [x] Received ', $msg->body, "\n";
    $data = time().' '.$msg->body."\n";
    $fnm = 'case_hello_'.date('Y-m-d_H-i-s').'.txt';
    file_put_contents($fnm, $data);
    changeRights($fnm);
};

$channel->basic_consume('hello', '', false, true, false, false, $callback);

while ($channel->is_open()) {
    $channel->wait();
}

$channel->close();
$connection->close();
