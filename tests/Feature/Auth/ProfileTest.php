<?php

namespace Tests\Feature\Auth;

use Tests\ApiTestCase;

class ProfileTest extends ApiTestCase
{
    public function test_authenticated_user_can_get_profile(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['name', 'email', 'role'],
                'success',
                'code',
                'message',
            ])
            ->assertJson([
                'success' => true,
                'code' => '0200',
                'data' => [
                    'name' => 'Admin User',
                    'email' => 'admin@example.com',
                    'role' => 'admin',
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $response = $this->getJson('/api/profile');

        $this->assertApiError($response, 401, '0401');
    }
}
