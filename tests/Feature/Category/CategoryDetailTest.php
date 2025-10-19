<?php

namespace Tests\Feature\Category;

use Tests\ApiTestCase;

class CategoryDetailTest extends ApiTestCase
{
    public function test_user_can_view_category_with_permission(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/categories/frontend');

        $this->assertApiSuccess($response);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertEquals('frontend', $response->json('data.slug'));
    }

    public function test_user_cannot_view_category_without_permission(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/categories/backend');

        $this->assertApiError($response, 404, '0404');
    }

    public function test_non_existent_category_returns_404(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'gold@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/categories/non-existent');

        $this->assertApiError($response, 404, '0404');
    }

    public function test_unauthenticated_user_cannot_access_category(): void
    {
        $response = $this->getJson('/api/categories/frontend');

        $this->assertApiError($response, 401, '0401');
    }
}
