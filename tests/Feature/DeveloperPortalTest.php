<?php

use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$', 'minor_unit' => 2, 'is_active' => true]);
    Currency::firstOrCreate(['code' => 'NGN'], ['name' => 'Nigerian Naira', 'symbol' => '₦', 'minor_unit' => 2, 'is_active' => true]);
    Currency::firstOrCreate(['code' => 'EUR'], ['name' => 'Euro', 'symbol' => '€', 'minor_unit' => 2, 'is_active' => true]);

    ExchangeRate::updateOrCreate(
        ['base_currency' => 'USD', 'quote_currency' => 'NGN', 'market_type' => 'parallel', 'rate_date' => now()->toDateString(), 'provider' => 'binance_p2p'],
        ['rate' => '1650.00', 'fetched_at' => now()]
    );
});

test('developer can navigate to keys, logs, and playground tabs with scoped card visibility', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $this->actingAs($user);

    // On 'keys' tab: KPI cards ARE visible
    Livewire::test('admin-dashboard', ['activeTab' => 'keys'])
        ->assertSet('activeTab', 'keys')
        ->assertSee('Generate New API Secret Key')
        ->assertSee('My Active API Keys')
        ->assertSee('Plan Tier');

    // On 'logs' tab: 4 KPI summary cards are hidden
    Livewire::test('admin-dashboard', ['activeTab' => 'logs'])
        ->assertSet('activeTab', 'logs')
        ->assertSee('Recent API Request Stream')
        ->assertDontSee('Plan Tier');

    // On 'playground' tab: 4 KPI summary cards are hidden
    Livewire::test('admin-dashboard', ['activeTab' => 'playground'])
        ->assertSet('activeTab', 'playground')
        ->assertSee('Interactive API Playground')
        ->assertDontSee('Plan Tier');
});

test('developer only sees logs for their own api keys and not other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $keyResult1 = ApiKey::createToken('User 1 App', $user1);
    $keyResult2 = ApiKey::createToken('User 2 App', $user2);

    $key1 = $keyResult1['apiKey'];
    $key2 = $keyResult2['apiKey'];

    ApiRequestLog::create([
        'api_key_id' => $key1->id,
        'ip_address' => '192.168.1.50',
        'endpoint' => 'api/v1/convert',
        'method' => 'GET',
        'status_code' => 200,
        'response_time_ms' => 15,
    ]);

    ApiRequestLog::create([
        'api_key_id' => $key2->id,
        'ip_address' => '10.0.0.99',
        'endpoint' => 'api/v1/rates/latest',
        'method' => 'GET',
        'status_code' => 200,
        'response_time_ms' => 25,
    ]);

    $this->actingAs($user1);

    Livewire::test('admin-dashboard', ['activeTab' => 'logs'])
        ->assertSee('User 1 App')
        ->assertDontSee('User 2 App')
        ->assertSee('api/v1/convert');
});

test('developer telemetry calculates total requests, success rate, and avg latency', function () {
    $user = User::factory()->create();
    $keyResult = ApiKey::createToken('Analytics Key', $user);
    $key = $keyResult['apiKey'];

    // 2 successful requests, 1 rate-limited request
    ApiRequestLog::create([
        'api_key_id' => $key->id,
        'ip_address' => '127.0.0.1',
        'endpoint' => 'api/v1/convert',
        'method' => 'GET',
        'status_code' => 200,
        'response_time_ms' => 10,
    ]);

    ApiRequestLog::create([
        'api_key_id' => $key->id,
        'ip_address' => '127.0.0.1',
        'endpoint' => 'api/v1/convert',
        'method' => 'GET',
        'status_code' => 200,
        'response_time_ms' => 20,
    ]);

    ApiRequestLog::create([
        'api_key_id' => $key->id,
        'ip_address' => '127.0.0.1',
        'endpoint' => 'api/v1/convert',
        'method' => 'GET',
        'status_code' => 429,
        'response_time_ms' => 6,
    ]);

    $this->actingAs($user);

    $component = Livewire::test('admin-dashboard', ['activeTab' => 'logs']);
    $metrics = $component->get('developerUsageMetrics');

    expect($metrics['total_requests'])->toBe('3')
        ->and($metrics['success_rate'])->toBe(66.7)
        ->and($metrics['error_count'])->toBe(1);
});

test('developer can execute live requests in the in-browser api playground', function () {
    $user = User::factory()->create();
    ApiKey::createToken('Playground Test Key', $user);

    $this->actingAs($user);

    Livewire::test('admin-dashboard', ['activeTab' => 'playground'])
        ->set('playgroundEndpoint', '/api/v1/convert')
        ->set('playgroundFrom', 'USD')
        ->set('playgroundTo', 'NGN')
        ->set('playgroundAmount', '100')
        ->set('playgroundMarket', 'parallel')
        ->call('executePlayground')
        ->assertSet('playgroundResponseStatus', 200)
        ->assertSee('165000') // 100 * 1650
        ->assertSet('playgroundSdkTab', 'curl')
        ->set('playgroundSdkTab', 'js')
        ->assertSee('const response = await fetch')
        ->set('playgroundSdkTab', 'python')
        ->assertSee('import requests')
        ->set('playgroundSdkTab', 'php')
        ->assertSee('Http::withToken');
});

test('developer with multiple keys can switch target key in playground and execute successfully', function () {
    $user = User::factory()->create();
    $key1 = ApiKey::createToken('Frontend Web App', $user)['apiKey'];
    $key2 = ApiKey::createToken('Mobile iOS App', $user)['apiKey'];

    $this->actingAs($user);

    Livewire::test('admin-dashboard', ['activeTab' => 'playground'])
        ->assertSee('Frontend Web App')
        ->assertSee('Mobile iOS App')
        ->set('playgroundKeyId', $key2->id)
        ->set('playgroundEndpoint', '/api/v1/convert')
        ->set('playgroundFrom', 'USD')
        ->set('playgroundTo', 'NGN')
        ->set('playgroundAmount', '200')
        ->set('playgroundMarket', 'parallel')
        ->call('executePlayground')
        ->assertSet('playgroundResponseStatus', 200)
        ->assertSee('330000'); // 200 * 1650
});
