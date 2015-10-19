<?php

namespace InterestCalculator;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use \Exception;

/**
 * Receives messages from  Rabbit MQ Server, handles them and sends result back.
 *
 * @package InterestCalculator
 */
class MessageHandler
{
    /**
     * @var Rabbit MQ connection
     */
    private $connection;

    /**
     * @var Rabbit MQ channel
     */
    private $channel;

    /**
     * @var Rabbit MQ listen queue name
     */
    private $listenQueue;

    /**
     * @var Rabbit MQ listen broadcast name
     */
    private $broadcastQueue;

    /**
     * @var \InterestCalculator\InterestCalculator
     */
    private $interestCaculator;

    /**
     * Connects to the Rabbit MQ Server
     *
     * @param $server
     * @param $user
     * @param $password
     * @param int $port
     */
    public function connect($server, $user, $password, $port = 5672)
    {
        $this->connection = new AMQPStreamConnection($server, $port, $user, $password);
        $this->channel    = $this->connection->channel();
    }

    /**
     * Receives the messages and pass them to sender
     */
    public function handle()
    {
        $this->channel->basic_consume($this->listenQueue, '', false, true, false, false, [$this, 'send']);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->connection->close();
    }


    /**
     * Callback for received Rabbit MQ Messages. Handles the message and broadcasts the result.
     * @param $msg
     */
    public function send($msg)
    {
        echo "Received ", $msg->body, "\n";

        $input = json_decode($msg->body, true);

        try {
            $output = $this->interestCaculator->caculateInterest($input);
            $output = json_encode($output);
            echo "Sending back ", $output, "\n";

            $this->channel->basic_publish(new AMQPMessage($output), '', $this->broadcastQueue);
        } catch (Exception $e) {
            echo "Unable to handle the message: ", $e->getMessage(), "\n";
        }
    }

    /**
     * @param \InterestCalculator\InterestCalculator $interestCaculator
     */
    public function setInterestCaculator(InterestCalculator $interestCaculator)
    {
        $this->interestCaculator = $interestCaculator;
    }

    /**
     * @param Rabbit $listenQueue
     */
    public function setListenQueue($listenQueue)
    {
        $this->listenQueue = $listenQueue;
    }

    /**
     * @param Rabbit $broadcastQueue
     */
    public function setBroadcastQueue($broadcastQueue)
    {
        $this->broadcastQueue = $broadcastQueue;
    }
}
