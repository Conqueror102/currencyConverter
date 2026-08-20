<?php

use App\Models\ApiKey;
use App\Models\User;
use Livewire\Livewire;

test('newly registered users are not administrators by default', function () {
    $user = User::factory()->create();

    expect($user->is_admin)->toBeFalse()
        ->and($user->isAdmin())->toBeFalse();
});

test('administrator users have admin privileges', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->is_admin)->toBeTrue()
        ->and($admin->isAdmin())->toBeTrue();
});

test('regular users only see their own api keys in the dashboard list', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    ApiKey::createToken('User 1 Key', $user1);
    ApiKey::createToken('User 2 Key', $user2);

    $this->actingAs($user1);

    Livewire::test('admin-dashboard')
        ->assertSee('User 1 Key')
        ->assertDontSee('User 2 Key');
});

test('admin users see all api keys across all accounts', function () {
    $admin = User::factory()->admin()->create();
    $dev1 = User::factory()->create(['name' => 'Alice Developer']);
    $dev2 = User::factory()->create(['name' => 'Bob Engineer']);

    ApiKey::createToken('Alice Production Key', $dev1);
    ApiKey::createToken('Bob Microservice Key', $dev2);

    $this->actingAs($admin);

    Livewire::test('admin-dashboard', ['activeTab' => 'keys'])
        ->assertSee('Alice Production Key')
        ->assertSee('Bob Microservice Key')
        ->assertSee('Alice Developer')
        ->assertSee('Bob Engineer');
});

test('regular users can create their own free api key associated with their user id', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('admin-dashboard')
        ->set('newKeyName', 'My Mobile App Token')
        ->call('createApiKey')
        ->assertSee('API Key generated successfully');

    $apiKey = ApiKey::where('name', 'My Mobile App Token')->first();

    expect($apiKey)->not->toBeNull()
        ->and($apiKey->user_id)->toBe($user->id)
        ->and($apiKey->tier)->toBe('free')
        ->and($apiKey->rate_limit_per_minute)->toBe(60);
});

test('regular users are blocked from executing administrative sync and rate overrides', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $this->actingAs($user);

    Livewire::test('admin-dashboard')
        ->call('syncOfficial')
        ->assertForbidden();

    Livewire::test('admin-dashboard')
        ->call('syncParallel')
        ->assertForbidden();

    Livewire::test('admin-dashboard')
        ->call('toggleCurrency', 'USD')
        ->assertForbidden();

    Livewire::test('admin-dashboard')
        ->call('editRate', 1)
        ->assertForbidden();
});

test('regular users cannot toggle or delete api keys owned by other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $tokenResult = ApiKey::createToken('User 2 Key', $user2);
    $keyId = $tokenResult['apiKey']->id;

    $this->actingAs($user1);

    Livewire::test('admin-dashboard')
        ->call('toggleApiKey', $keyId)
        ->assertForbidden();

    Livewire::test('admin-dashboard')
        ->call('deleteApiKey', $keyId)
        ->assertForbidden();
});

test('regular users can toggle and delete their own api keys', function () {
    $user = User::factory()->create();
    $tokenResult = ApiKey::createToken('My Removable Key', $user);
    $apiKey = $tokenResult['apiKey'];

    $this->actingAs($user);

    Livewire::test('admin-dashboard')
        ->call('toggleApiKey', $apiKey->id)
        ->assertOk();

    expect($apiKey->fresh()->is_active)->toBeFalse();

    Livewire::test('admin-dashboard')
        ->call('deleteApiKey', $apiKey->id)
        ->assertOk();

    expect(ApiKey::find($apiKey->id))->toBeNull();
});

test('users can open revoke modal, cancel it, or confirm it to delete an api key', function () {
    $user = User::factory()->create();
    $tokenResult = ApiKey::createToken('Modal Revoke Key', $user);
    $apiKey = $tokenResult['apiKey'];

    $this->actingAs($user);

    Livewire::test('admin-dashboard')
        ->call('confirmRevokeApiKey', $apiKey->id)
        ->assertSet('revokingApiKeyId', $apiKey->id)
        ->assertSet('revokingApiKeyName', 'Modal Revoke Key')
        ->call('cancelRevokeApiKey')
        ->assertSet('revokingApiKeyId', null)
        ->call('confirmRevokeApiKey', $apiKey->id)
        ->call('deleteApiKey')
        ->assertSet('revokingApiKeyId', null);

    expect(ApiKey::find($apiKey->id))->toBeNull();
});
