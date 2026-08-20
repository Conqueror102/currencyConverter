<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('guests are redirected from the system guide to login', function () {
    $response = $this->get(route('admin.guide'));
    $response->assertRedirect(route('login'));
});

test('non-admin users are forbidden from the system guide', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $this->actingAs($user);

    $response = $this->get(route('admin.guide'));
    $response->assertForbidden();
});

test('admin users can visit the system guide', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $response = $this->get(route('admin.guide'));
    $response->assertOk()
        ->assertSee('System Architecture');
});
