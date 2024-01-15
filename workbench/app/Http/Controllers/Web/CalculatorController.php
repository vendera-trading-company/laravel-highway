<?php

namespace Workbench\App\Http\Controllers\Web;

use VenderaTradingCompany\LaravelHighway\HighwayController;
use Workbench\App\Entities\Calculator;

class CalculatorController extends HighwayController
{
    protected static $entity = Calculator::class;
}
