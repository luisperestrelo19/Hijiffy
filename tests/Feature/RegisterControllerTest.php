<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    public function test_register_successful()
    {
        $payload = [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson(route('register'), $payload);

        $response->assertOk()
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name'  => 'Test User',
        ]);
    }

    public function test_register_with_duplicate_email_fails()
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $payload = [
            'name'                  => 'Another User',
            'email'                 => 'test@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson(route('register'), $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_with_missing_fields_fails()
    {
        $payload = [
            'name'     => '',
            'email'    => '',
            'password' => '',
        ];

        $response = $this->postJson(route('register'), $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
