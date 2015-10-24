<?php

namespace InterestCalculator;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Factory for building AMQP objects
 *
 * @package InterestCalculator
 */
class AMQPFactory implements IAMQPFactory
{
    public function buildConnection($server, $port, $user, $password)
    {
        return new AMQPStreamConnection($server, $port, $user, $password);
    }

    public function buildMessage($message)
    {
        return new AMQPMessage($message);
    }
}
