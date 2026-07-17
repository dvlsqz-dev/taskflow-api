<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a user to register', function () {
    $response = $this->postJson('/api/auth/register', [
        'name'                  => 'Daniel Velásquez',
        'email'                 => 'daniel@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(201)
             ->assertJsonPath('success', true)
             ->assertJsonStructure([
                 'data' => ['user', 'token', 'token_type']
             ]);
});

it('allows a user to login', function (){
    // Arrange
    $user = User::factory()->create([
        'email' => 'daniel@example.com'
    ]);

    // Act
    $response = $this->postJson('/api/auth/login', [
        'email' => 'daniel@example.com',
        'password' => 'password'
    ]);

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'token',
                'token_type'
            ]
        ]);
});

it('prevents login with invalid credentials', function (){
    // Arrange
    
    // Act
    $response = $this->postJson('/api/auth/login', [
        'email'    => 'noexiste@example.com',
        'password' => 'wrongpassword',
    ]);

    // Assert
    $response->assertStatus(401)
        ->assertJsonPath('success', false);
});

it('gets the authenticated user details', function (){
    // Arrange
    $user = User::factory()->create([
        'email' => 'daniel@example.com'
    ]);

    // Act
    $response = $this->actingAs($user, 'sanctum')->getJson('/api/auth/me');

    // Assert
    $response->assertStatus(200)
         ->assertJsonPath('success', true)
         ->assertJsonStructure([
             'data' => ['id', 'name', 'email']
         ]);
});
