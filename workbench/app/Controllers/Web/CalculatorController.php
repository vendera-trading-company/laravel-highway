<?php

namespace Workbench\App\Controllers\Web;

use VenderaTradingCompany\LaravelHighway\HighwayController;
use Workbench\App\Entities\Calculator;

class CalculatorController extends HighwayController
{
    protected static $entity = Calculator::class;
}
