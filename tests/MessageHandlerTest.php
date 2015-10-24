<?php

namespace InterestCalculator\Testing;

use InterestCalculator\MessageHandler;

use \Mockery as m;

class MessageHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \InterestCalculator\MessageHandler
     */
    private $messageHandler;


    public function setUp()
    {
        $this->messageHandler = new MessageHandler();
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @dataProvider canHandleDataProvider
     * @param $doException
     * @param $server
     * @param $user
     * @param $password
     * @param $port
     * @param $listenQueue
     * @param $broadcastQueue
     * @param $token
     * @param $calcInput
     * @param $calcResult
     * @param $broadcastMessage
     */
    public function testCanHandle($doException, $server, $user, $password, $port, $listenQueue, $broadcastQueue, $token, $calcInput, $calcResult, $broadcastMessage)
    {
        $amqpChannelMock            = m::mock('PhpAmqpLib\Channel\AMQPChannel');
        $amqpChannelMock->callbacks = [
            function () {
            },
        ];

        $amqpChannelMock->shouldReceive('close')->once()->withNoArgs();
        $amqpChannelMock->shouldReceive('wait')->times(1)->withNoArgs()->andSet('callbacks', []);

        $amqpMessageMock = m::mock('PhpAmqpLib\Message\AMQPMessage');

        if (!$doException) {
            $amqpChannelMock->shouldReceive('basic_publish')->once()->with($amqpMessageMock, '', $broadcastQueue);
        }

        $amqpStreamConnectionMock = m::mock('PhpAmqpLib\Connection\AMQPStreamConnection');
        $amqpStreamConnectionMock->shouldReceive('channel')->once()->withNoArgs()->andReturn($amqpChannelMock);
        $amqpStreamConnectionMock->shouldReceive('close')->once()->withNoArgs();

        $amqpFactoryMock = m::mock('InterestCalculator\AMQPFactory');
        $amqpFactoryMock->shouldReceive('buildConnection')->once()->with($server, $port, $user, $password)->andReturn($amqpStreamConnectionMock);

        $msg       = m::mock();
        $msg->body = json_encode($calcInput);


        $climateMock = m::mock();
        $climateMock->shouldReceive('br')->once()->withNoArgs();
        $climateMock->shouldReceive('info')->once()->with('Received: '.$msg->body);

        if (!$doException) {
            $amqpFactoryMock->shouldReceive('buildMessage')->once()->with($broadcastMessage)->andReturn($amqpMessageMock);
        }


        $interestCalculatorMock = m::mock('InterestCalculator\Calculator');

        if (!$doException) {
            $interestCalculatorMock->shouldReceive('caculateInterest')->once()->with($calcInput)->andReturn($calcResult);
            $climateMock->shouldReceive('out')->once()->with('Sending back: '.$broadcastMessage);
        } else {
            $interestCalculatorMock->shouldReceive('caculateInterest')->once()->with($calcInput)->andThrow('InvalidArgumentException', 'Sum is not set.');
            $climateMock->shouldReceive('error')->once()->with('Unable to handle the message: Sum is not set.');
        }

        $this->messageHandler->setAmqpFactory($amqpFactoryMock);
        $this->messageHandler->setInterestCalculator($interestCalculatorMock);
        $this->messageHandler->setClimate($climateMock);
        $this->messageHandler->setListenQueue($listenQueue);
        $this->messageHandler->setBroadcastQueue($broadcastQueue);
        $this->messageHandler->setToken($token);
        $this->messageHandler->connect($server, $user, $password, $port);

        $amqpChannelMock->shouldReceive('basic_consume')->once()->with(
            $listenQueue,
            '',
            false,
            true,
            false,
            false,
            m::on(function ($closure) use ($msg, $doException) {
                return call_user_func($closure, $msg) === !$doException;
            })
        );


        $this->messageHandler->handle();
    }

    public function canHandleDataProvider()
    {
        $server         = 'myserver.com';
        $user           = 'user';
        $password       = 'secret';
        $port           = 5555;
        $listenQueue    = 'listen-queue';
        $broadcastQueue = 'broadcast-queue';
        $token          = 'phpunit';

        $calcInput        = ['days' => 5, 'sum' => 123];
        $calcResult       = ['days' => 5, 'sum' => 123, 'interest' => 18.45, 'totalSum' => 141.45];
        $broadcastMessage = json_encode(['days' => 5, 'sum' => 123, 'interest' => 18.45, 'totalSum' => 141.45, 'token' => $token]);

        return [
            [
                $doException = false, $server, $user, $password, $port, $listenQueue, $broadcastQueue, $token, $calcInput, $calcResult, $broadcastMessage
            ],
            [
                $doException = true, $server, $user, $password, $port, $listenQueue, $broadcastQueue, $token, $calcInput, $calcResult, $broadcastMessage
            ],
        ];
    }
}
