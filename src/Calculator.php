<?php

namespace InterestCalculator;

use \InvalidArgumentException;

class Calculator implements ICalculator
{
    public function caculateInterest(array $input)
    {
        if (!array_key_exists('days', $input) || (int)$input['days'] < 1) {
            throw new InvalidArgumentException('Days is not set.');
        }

        if (!array_key_exists('sum', $input) || (int)$input['sum'] < 1) {
            throw new InvalidArgumentException('Sum is not set.');
        }

        $days = (int)$input['days'];
        $sum  = (int)$input['sum'];

        // Will count number or days for each interest percent
        $countInterest[1] = 0;
        $countInterest[2] = 0;
        $countInterest[3] = 0;
        $countInterest[4] = 0;

        // set scale of decimal places to 2
        bcscale(2);

        // counting number of days that are divisible by 3, 5, 3 and 5 using Inclusion-exclusion formula.
        $daysDivBy3       = floor($days / 3);
        $daysDivBy5       = floor($days / 5);
        $daysDivBy3and5   = floor($days / (3 * 5));
        $daysDivBy3or5    = $daysDivBy3 + $daysDivBy5 - $daysDivBy3and5;
        $daysNotDivBy3or5 = $days - $daysDivBy3or5;

        $countInterest[1] = $daysDivBy3 - $daysDivBy3and5;
        $countInterest[2] = $daysDivBy5 - $daysDivBy3and5;
        $countInterest[3] = $daysDivBy3and5;
        $countInterest[4] = $daysNotDivBy3or5;

        // calculating total interest and sum by given formula
        $totalInterest = 0;
        foreach ($countInterest as $interestPercent => $daysCount) {
            $interestAmount = bcmul(bcmul(bcdiv($sum, 100), $interestPercent), $daysCount);
            $totalInterest  = bcadd($totalInterest, $interestAmount);
        }

        $totalSum = bcadd($sum, $totalInterest);
        
        return array_merge($input, [
            'interest' => (float)$totalInterest,
            'totalSum' => (float)$totalSum,
        ]);

    }
}
