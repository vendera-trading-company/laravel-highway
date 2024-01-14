<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CalculatorMultiplyTest extends TestCase
{
    public function testResultIsCorrect()
    {
        $number = 123;
        $multiplier = 423;

        $result = $number * $multiplier;

        $response = $this->post(route('web.calculator.multiply'), [
            'number' => $number,
            'multiplier' => $multiplier
        ]);

        $response->assertOk();

        $this->assertEquals($result, $response->json('result'));
    }

    public function testApiResultIsCorrect()
    {
        $number = 123;
        $multiplier = 423;

        $result = $number * $multiplier;

        $response = $this->post(route('api.calculator.multiply'), [
            'number' => $number,
            'multiplier' => $multiplier
        ]);

        $response->assertOk();

        $this->assertEquals($result, $response->json('result'));
    }
}
