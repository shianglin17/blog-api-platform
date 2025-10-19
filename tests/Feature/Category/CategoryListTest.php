<?php

namespace Tests\Feature\Category;

use Tests\ApiTestCase;

class CategoryListTest extends ApiTestCase
{
    public function test_normal_user_can_only_see_one_category(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/categories');

        $this->assertApiSuccess($response);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('frontend', $data[0]['slug']);
    }

    public function test_silver_user_can_see_two_categories(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'silver@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/categories');

        $this->assertApiSuccess($response);

        $data = $response->json('data');
        $this->assertCount(2, $data);
    }

    public function test_gold_user_can_see_all_categories(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'gold@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/categories');

        $this->assertApiSuccess($response);

        $data = $response->json('data');
        $this->assertCount(3, $data);
    }

    public function test_pagination_structure_is_correct(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'gold@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/categories');

        $response->assertJsonStructure([
            'data',
            'success',
            'code',
            'message',
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'per_page',
                'to',
                'total',
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
        ]);
    }

    public function test_category_structure_is_correct(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/categories');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'accessible_articles_count',
                ],
            ],
        ]);
    }

    public function test_accessible_articles_count_is_correct(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/categories');

        $data = $response->json('data');
        $this->assertEquals(3, $data[0]['accessible_articles_count']);
    }

    public function test_unauthenticated_user_cannot_access_categories(): void
    {
        $response = $this->getJson('/api/categories');

        $this->assertApiError($response, 401, '0401');
    }
}
