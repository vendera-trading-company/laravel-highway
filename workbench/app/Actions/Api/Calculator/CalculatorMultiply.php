<?php

namespace Workbench\App\Actions\Api\Calculator;

use VenderaTradingCompany\PHPActions\Action;

class CalculatorMultiply extends Action
{
    public function handle()
    {
        $number = $this->getData('number');
        $multiplier = $this->getData('multiplier');

        return response()->json([
            'result' => $number * $multiplier
        ]);
    }
}
