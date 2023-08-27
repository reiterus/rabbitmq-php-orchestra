<?php

$rmqHost = $_ENV['RMQ_HOST'] ?? 'rabbitmq';
$rmqPort = $_ENV['RMQ_PORT'] ?? 5672;
$rmqPortAdmin = $_ENV['RMQ_PORT_ADMIN'] ?? 15672;
$rmqLogin = $_ENV['RMQ_LOGIN'] ?? 'rabbitmq';
$rmqPassword = $_ENV['RMQ_PASSWORD'] ?? 'rabbitmq';
$rmqUrlAdmin = sprintf('http://%s:%d', $rmqHost, $rmqPortAdmin);

if (!@file_get_contents($rmqUrlAdmin)) {
    echo ">>>>> Oops... RabbitMQ is not ready yet...\n";
    exit(255);
}

function loadSimulation(int $repeats = 100000000)
{
    $rmqStressTest = $_ENV['RMQ_STRESS_TEST'] ?? 0;

    if (!$rmqStressTest) {
        echo ">>> WARNING! Stress Testing Disabled!\n";
        return;
    }

    for ($i=0;$i<$repeats;$i++) {
        echo '.';
    }

    echo "\n";
}

function changeRights(string $filePath)
{
    chown($filePath, 1000);
    chgrp($filePath, 1000);
}
