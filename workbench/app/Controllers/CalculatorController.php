<?php

namespace Workbench\App\Controllers;

use VenderaTradingCompany\LaravelHighway\HighwayController;
use Workbench\App\Entities\Calculator;

class CalculatorController extends HighwayController
{
    protected static $entity = Calculator::class;
}
