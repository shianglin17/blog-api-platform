<?php

namespace Tests\Feature\Auth;

use Tests\ApiTestCase;

class RefreshTokenTest extends ApiTestCase
{

    public function test_user_can_refresh_token_with_valid_refresh_token(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $refreshToken = $loginResponse->json('data.refresh_token');
        $oldAccessToken = $loginResponse->json('data.access_token');

        $response = $this->postJson('/api/refresh-token', [
            'refresh_token' => $refreshToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['access_token', 'refresh_token', 'token_type'],
                'success',
                'code',
                'message',
            ])
            ->assertJson([
                'success' => true,
                'code' => '0200',
                'data' => ['token_type' => 'Bearer'],
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data['access_token']);
        $this->assertNotEmpty($data['refresh_token']);
        $this->assertNotEquals($oldAccessToken, $data['access_token']);
        $this->assertNotEquals($refreshToken, $data['refresh_token']);

        $this->assertDatabaseHas('refresh_tokens', [
            'token' => hash('sha256', $data['refresh_token']),
        ]);
        $this->assertDatabaseMissing('refresh_tokens', [
            'token' => hash('sha256', $refreshToken),
        ]);
    }

    public function test_user_cannot_refresh_with_invalid_token(): void
    {
        $response = $this->postJson('/api/refresh-token', [
            'refresh_token' => str_repeat('a', 64),
        ]);

        $this->assertApiError($response, 401, '0401');
    }

    public function test_user_cannot_refresh_with_expired_token(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $refreshToken = $loginResponse->json('data.refresh_token');

        \App\Models\RefreshToken::where('token', hash('sha256', $refreshToken))
            ->update(['expires_at' => now()->subDay()]);

        $response = $this->postJson('/api/refresh-token', [
            'refresh_token' => $refreshToken,
        ]);

        $this->assertApiError($response, 401, '0401');

        $this->assertDatabaseMissing('refresh_tokens', [
            'token' => hash('sha256', $refreshToken),
        ]);
    }

    public function test_refresh_token_requires_refresh_token_field(): void
    {
        $response = $this->postJson('/api/refresh-token', []);

        $this->assertApiError($response, 422, '0422');
    }

    public function test_old_refresh_token_cannot_be_reused_after_refresh(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $refreshToken = $loginResponse->json('data.refresh_token');

        $this->postJson('/api/refresh-token', [
            'refresh_token' => $refreshToken,
        ])->assertStatus(200);

        $response = $this->postJson('/api/refresh-token', [
            'refresh_token' => $refreshToken,
        ]);

        $this->assertApiError($response, 401, '0401');
    }
}
