<?php

namespace InterestCalculator;

use League\CLImate\CLImate;

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../config.php';

$messageHandler = new MessageHandler();
$messageHandler->setAmqpFactory(new AMQPFactory());
$messageHandler->setInterestCalculator(new Calculator());
$messageHandler->setClimate(new CLImate());
$messageHandler->setListenQueue(RABBIT_MQ_SERVER_LISTEN_QUEUE);
$messageHandler->setBroadcastQueue(RABBIT_MQ_SERVER_BROADCAST_QUEUE);
$messageHandler->setToken(MESSAGE_TOKEN);
$messageHandler->connect(RABBIT_MQ_SERVER_HOST, RABBIT_MQ_SERVER_USER, RABBIT_MQ_SERVER_PASSWORD);
$messageHandler->handle();
