<?php

namespace Tests\Feature\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->postJson('/api/auth/login', ['email' => 'test@example.com', 'password' => 'secret'])
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('secret'),
        ]);

        $this->postJson('/api/auth/login', ['email' => 'test@example.com', 'password' => 'wrong'])
            ->assertStatus(401)
            ->assertJson(['message' => 'Email atau password salah.']);
    }

    public function test_login_fails_with_invalid_email_format(): void
    {
        $this->postJson('/api/auth/login', ['email' => 'bukan-email', 'password' => 'pass'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_fails_with_missing_fields(): void
    {
        $this->postJson('/api/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->deleteJson('/api/auth/logout')
            ->assertStatus(200)
            ->assertJson(['message' => 'Logout berhasil.']);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/dashboard')
            ->assertStatus(401);
    }
}
