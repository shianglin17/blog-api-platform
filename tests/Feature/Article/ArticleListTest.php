<?php

namespace Tests\Feature\Article;

use Tests\ApiTestCase;

class ArticleListTest extends ApiTestCase
{
    public function test_normal_user_can_see_three_articles(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/articles');

        $this->assertApiSuccess($response);

        $data = $response->json('data');
        $this->assertCount(3, $data);
    }

    public function test_silver_user_can_see_six_articles(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'silver@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/articles');

        $this->assertApiSuccess($response);

        $data = $response->json('data');
        $this->assertCount(6, $data);
    }

    public function test_gold_user_can_see_all_articles(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'gold@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/articles');

        $this->assertApiSuccess($response);

        $data = $response->json('data');
        $this->assertCount(9, $data);
    }

    public function test_filter_articles_by_category(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/articles?category=frontend');

        $this->assertApiSuccess($response);

        $data = $response->json('data');
        $this->assertCount(3, $data);

        foreach ($data as $article) {
            $this->assertEquals('frontend', $article['category']['slug']);
        }
    }

    public function test_pagination_structure_is_correct(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'gold@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/articles');

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

    public function test_article_structure_is_correct(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/articles');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'description',
                    'category' => [
                        'id',
                        'name',
                        'slug',
                    ],
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_unauthenticated_user_cannot_access_articles(): void
    {
        $response = $this->getJson('/api/articles');

        $this->assertApiError($response, 401, '0401');
    }
}
