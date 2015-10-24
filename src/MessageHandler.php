<?php

namespace InterestCalculator;

use \Exception;
use League\CLImate\CLImate;

/**
 * Receives messages from Rabbit MQ Server, handles them and sends result back.
 *
 * @package InterestCalculator
 */
class MessageHandler
{
    /**
     * @var \PhpAmqpLib\Connection\AMQPStreamConnection Rabbit MQ connection
     */
    private $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel Rabbit MQ channel
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
     * @var Identifier for messages
     */
    private $token;

    /**
     * @var \InterestCalculator\ICalculator
     */
    private $interestCalculator;

    /**
     * @var \InterestCalculator\IAMQPFactory
     */
    private $amqpFactory;

    /**
     * @var \League\CLImate\CLImate
     */
    private $climate;

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
        $this->connection = $this->amqpFactory->buildConnection($server, $port, $user, $password);
        $this->channel    = $this->connection->channel();
    }

    /**
     * Receives the messages and pass them to sender
     */
    public function handle()
    {

        $this->channel->basic_consume($this->listenQueue, '', false, true, false, false, function ($msg) {
            $this->climate->br();
            $this->climate->info('Received: '.$msg->body);
            $input = json_decode($msg->body, true);

            try {
                $output          = $this->interestCalculator->caculateInterest($input);
                $output['token'] = $this->token;
                $output          = json_encode($output);
                $this->climate->out('Sending back: '.$output);

                $this->channel->basic_publish($this->amqpFactory->buildMessage($output), '', $this->broadcastQueue);
            } catch (Exception $e) {
                $this->climate->error('Unable to handle the message: '.$e->getMessage());
                return false;
            }

            return true;
        });


        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @param IAMQPFactory $amqpFactory
     */
    public function setAmqpFactory(IAMQPFactory $amqpFactory)
    {
        $this->amqpFactory = $amqpFactory;
    }

    /**
     * @param \InterestCalculator\ICalculator $interestCalculator
     */
    public function setInterestCalculator(ICalculator $interestCalculator)
    {
        $this->interestCalculator = $interestCalculator;
    }

    /**
     * @param string $listenQueue
     */
    public function setListenQueue($listenQueue)
    {
        $this->listenQueue = $listenQueue;
    }

    /**
     * @param string $broadcastQueue
     */
    public function setBroadcastQueue($broadcastQueue)
    {
        $this->broadcastQueue = $broadcastQueue;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param CLImate $climate
     */
    public function setClimate($climate)
    {
        $this->climate = $climate;
    }
}
