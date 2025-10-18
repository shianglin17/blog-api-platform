<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
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
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data['access_token']);
        $this->assertNotEmpty($data['refresh_token']);
        $this->assertEquals('Bearer', $data['token_type']);

        $this->assertDatabaseHas('refresh_tokens', [
            'token' => hash('sha256', $data['refresh_token']),
        ]);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['success', 'code', 'message'])
            ->assertJson(['success' => false, 'code' => '0401']);

        $this->assertDatabaseCount('refresh_tokens', 0);
    }

    public function test_user_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'notexist@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['success', 'code', 'message'])
            ->assertJson(['success' => false, 'code' => '0401']);
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonStructure(['success', 'code', 'message'])
            ->assertJson(['success' => false, 'code' => '0422']);
    }
}
