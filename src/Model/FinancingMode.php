<?php

namespace App\Model;

enum FinancingMode: string
{
    case Linear = 'linear';
    case Formula1 = 'f1';
    case Formula2 = 'f2';
    case Formula3 = 'f3';

    public function firstPaymentRate()
    {
        return match($this) {
            FinancingMode::Linear => 0,
            FinancingMode::Formula1 => 0.1,
            FinancingMode::Formula2 => 0.2,
            FinancingMode::Formula3 => 0.3,
        };
    }

    public function getHumanLabel()
    {
        return match($this) {
            FinancingMode::Linear => 'Lineaire',
            FinancingMode::Formula1 => '1er loyer 10%',
            FinancingMode::Formula2 => '1er loyer 20%',
            FinancingMode::Formula3 => '1er loyer 30%',
        };
    }
}
