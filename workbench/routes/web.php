<?php

use Illuminate\Support\Facades\Route;
use Workbench\App\Entities\Calculator;
use Workbench\App\Routing\Api;
use Workbench\App\Routing\Web;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Web::get(Calculator::class, 'multiply', 'multiply');
Api::post(Calculator::class, 'multiply', 'multiply');
