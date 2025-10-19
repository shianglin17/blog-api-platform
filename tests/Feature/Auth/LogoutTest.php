<?php

namespace Tests\Feature\Auth;

use Tests\ApiTestCase;

class LogoutTest extends ApiTestCase
{
    public function test_authenticated_user_can_logout(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');
        $refreshToken = $loginResponse->json('data.refresh_token');

        $this->assertDatabaseHas('refresh_tokens', [
            'token' => hash('sha256', $refreshToken),
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $response = $this->withToken($token)->postJson('/api/logout');

        $this->assertApiSuccess($response);

        $this->assertDatabaseMissing('refresh_tokens', [
            'token' => hash('sha256', $refreshToken),
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->postJson('/api/logout');

        $this->assertApiError($response, 401, '0401');
    }
}
