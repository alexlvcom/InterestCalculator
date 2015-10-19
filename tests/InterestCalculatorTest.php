<?php

namespace InterestCalculator\Testing;

use InterestCalculator\InterestCalculator;

class ServiceContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \InterestCalculator\InterestCalculator
     */
    private $calculator;

    public function setUp()
    {
        $this->calculator = new InterestCalculator();
    }

    /**
     * @dataProvider canCalculateInterestDataProvider
     * @param $input
     * @param $output
     */
    public function testCanCalculateInterest($input, $output)
    {
        $this->assertEquals($output, $this->calculator->caculateInterest($input));
    }

    /**
     * @dataProvider canThrowExceptionsDataProvider
     * @param $input
     * @param $exceptionMsg
     */
    public function testCanThrowExceptions($input, $exceptionMsg)
    {

        $this->setExpectedException('InvalidArgumentException', $exceptionMsg);
        $this->calculator->caculateInterest($input);
    }

    public function canCalculateInterestDataProvider()
    {
        return [
            [['sum' => 123, 'days' => 5], ['sum' => 123, 'days' => 5, 'interest' => 18.45, 'totalSum' => 141.45, 'token' => 'MyIdentifier']],
            [['sum' => 456, 'days' => 16], ['sum' => 456, 'days' => 16, 'interest' => 214.32, 'totalSum' => 670.32, 'token' => 'MyIdentifier']]
        ];
    }

    public function canThrowExceptionsDataProvider()
    {
        return [
            [['sum' => 123], 'Days is not set.'],
            [['days' => 5], 'Sum is not set.'],
        ];
    }
}
