<?php

use App\Models\Currency;

test('currencies endpoint lists active currencies', function () {
    Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'minor_unit' => 2, 'is_active' => true]);
    Currency::create(['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'minor_unit' => 2, 'is_active' => true]);

    $response = $this->getJson('/api/v1/currencies');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['code', 'name', 'numeric_code', 'symbol', 'minor_unit'],
            ],
            'meta' => ['count'],
        ]);
});
