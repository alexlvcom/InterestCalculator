<?php

namespace InterestCalculator\Testing;

use InterestCalculator\Calculator;

class ServiceContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \InterestCalculator\ICalculator
     */
    private $calculator;

    public function setUp()
    {
        $this->calculator = new Calculator();
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
            [$input = ['days' => 5, 'sum' => 123], array_merge($input, ['interest' => 18.45, 'totalSum' => 141.45])],
            [$input = ['days' => 29, 'sum' => 196], array_merge($input, ['interest' => 162.68, 'totalSum' => 358.68])],
            [$input = ['days' => 1, 'sum' => 221], array_merge($input, ['interest' => 8.84, 'totalSum' => 229.84])],
            [$input = ['days' => 3, 'sum' => 921], array_merge($input, ['interest' => 82.89, 'totalSum' => 1003.89])],
            [$input = ['days' => 19, 'sum' => 932], array_merge($input, ['interest' => 521.92, 'totalSum' => 1453.92])],
            [$input = ['days' => 18, 'sum' => 488], array_merge($input, ['interest' => 253.76, 'totalSum' => 741.76])],
            [$input = ['days' => 26, 'sum' => 18], array_merge($input, ['interest' => 13.32, 'totalSum' => 31.32])],
            [$input = ['days' => 6, 'sum' => 749], array_merge($input, ['interest' => 119.84, 'totalSum' => 868.84])],
            [$input = ['days' => 11, 'sum' => 158], array_merge($input, ['interest' => 48.98, 'totalSum' => 206.98])],
            [$input = ['days' => 1, 'sum' => 695], array_merge($input, ['interest' => 27.8, 'totalSum' => 722.8])],
            [$input = ['days' => 3, 'sum' => 141], array_merge($input, ['interest' => 12.69, 'totalSum' => 153.69])],
            [$input = ['days' => 25, 'sum' => 251], array_merge($input, ['interest' => 175.7, 'totalSum' => 426.7])],
            [$input = ['days' => 20, 'sum' => 559], array_merge($input, ['interest' => 324.22, 'totalSum' => 883.22])],
            [$input = ['days' => 1, 'sum' => 778], array_merge($input, ['interest' => 31.12, 'totalSum' => 809.12])],
            [$input = ['days' => 4, 'sum' => 809], array_merge($input, ['interest' => 105.17, 'totalSum' => 914.17])],
            [$input = ['days' => 22, 'sum' => 597], array_merge($input, ['interest' => 376.11, 'totalSum' => 973.11])],
            [$input = ['days' => 14, 'sum' => 350], array_merge($input, ['interest' => 140, 'totalSum' => 490])],
            [$input = ['days' => 24, 'sum' => 896], array_merge($input, ['interest' => 609.28, 'totalSum' => 1505.28])],
            [$input = ['days' => 10, 'sum' => 114], array_merge($input, ['interest' => 30.78, 'totalSum' => 144.78])],
            [$input = ['days' => 14, 'sum' => 359], array_merge($input, ['interest' => 143.6, 'totalSum' => 502.6])],
            [$input = ['days' => 12, 'sum' => 824], array_merge($input, ['interest' => 263.68, 'totalSum' => 1087.68])],
            [$input = ['days' => 238934205, 'sum' => 169], array_merge($input, ['interest' => 1157556578.49, 'totalSum' => 1157556747.49])],
            [$input = ['days' => 114149722, 'sum' => 681], array_merge($input, ['interest' => 2228430872.43, 'totalSum' => 2228431553.43])],
            [$input = ['days' => 285668047, 'sum' => 217], array_merge($input, ['interest' => 1777045697.56, 'totalSum' => 1777045914.56])],
            [$input = ['days' => 26679989, 'sum' => 480], array_merge($input, ['interest' => 367116648, 'totalSum' => 367117128])],
        ];
    }

    public function canThrowExceptionsDataProvider()
    {
        return [
            [['sum' => 123], 'Days is not set.'],
            [['days' => 5], 'Sum is not set.'],
            [['days' => -10, 'sum' => 56], 'Days is not set.'],
            [['days' => 6, 'sum' => null], 'Sum is not set.'],
        ];
    }
}
