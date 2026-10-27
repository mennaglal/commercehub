<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->seed(RolesSeeder::class);
});

it('registers a new user', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Menna',
        'email' => 'menna@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('users', ['email' => 'menna@example.com']);
});

it('rejects a duplicate email', function () {
    User::factory()->create(['email' => 'menna@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Someone Else',
        'email' => 'menna@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['email']);
    $this->assertDatabaseCount('users', 1);
});

it('does not allow guests to view the profile', function () {
    $this->getJson('/api/profile')->assertUnauthorized();
});



it('shows the profile of the logged-in user', function () {
    $user = User::factory()->create(['name' => 'Menna']);
    Sanctum::actingAs($user);

    $this->getJson('/api/profile')
        ->assertOk()
        ->assertJsonPath('data.user.name', 'Menna');
});
