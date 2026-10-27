<?php

it('requires email and password to log in', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['email', 'password']);
});

it('rejects an invalid email format', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'not-an-email',
        'password' => 'secret123',
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['email']);
    $response->assertJsonMissingValidationErrors(['password']);
});
