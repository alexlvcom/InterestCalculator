<?php

namespace InterestCalculator;

use \InvalidArgumentException;

class InterestCalculator
{
    /**
     * Calculates Total Interest and Sum
     *
     * @param array $input having days and sum keys set
     * @throws InvalidArgumentException
     * @return array
     */
    public function caculateInterest(array $input)
    {
        if (!array_key_exists('days', $input)) {
            throw new InvalidArgumentException('Days is not set.');
        }

        if (!array_key_exists('sum', $input)) {
            throw new InvalidArgumentException('Sum is not set.');
        }

        $days          = (int)$input['days'];
        $sum           = (int)$input['sum'];
        $totalInterest = 0;

        foreach (range(1, $days) as $day) {
            $interestPercent = 4;

            if ($day % 3 === 0) {
                $interestPercent = 1;
            }
            if ($day % 5 === 0) {
                $interestPercent = 2;
            }
            if ($day % 3 === 0 && $day % 5 === 0) {
                $interestPercent = 3;
            }

            $interestAmount = round(($sum / 100) * $interestPercent, 2);
            $totalInterest += $interestAmount;
        }

        $totalSum = $sum + $totalInterest;

        return array_merge($input, [
            'interest' => $totalInterest,
            'totalSum' => $totalSum,
            'token'    => 'MyIdentifier',
        ]);
    }
}
