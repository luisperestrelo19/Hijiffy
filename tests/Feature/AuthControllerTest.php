<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function test_login_successful()
    {
        $password = 'secret123';
        $user     = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make($password),
        ]);

        $response = $this->postJson(route('login'), [
            'email'    => 'test@example.com',
            'password' => $password,
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email'],
            ]);
    }

    public function test_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this->postJson(route('login'), [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_login_with_missing_fields()
    {
        $response = $this->postJson(route('login'), [
            'email'    => '',
            'password' => '',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_logout_successful()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('logout'));

        $response->assertOk()
            ->assertJson([
                'message' => 'Logged out',
            ]);
    }

    public function test_rate_limit_login_exceed()
    {
        $this->withoutExceptionHandling([ThrottleRequestsException::class]);

        for ($i = 0; $i < config('hijiffy.rate_limits.guest.limit') + 1; $i++) {
            $response = $this->postJson(route('login'), [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);
        }

        $response->assertTooManyRequests();
    }
}
