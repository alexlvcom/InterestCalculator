<?php

namespace InterestCalculator;

interface IAMQPFactory
{
    public function buildConnection($server, $port, $user, $password);

    public function buildMessage($message);
}
