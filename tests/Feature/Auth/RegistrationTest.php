<?php

test('registration is disabled and register route returns 404', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});

test('registration post is disabled and returns 404', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(404);
    $this->assertGuest();
});
