<?php

namespace Tests\Feature\Article;

use Tests\ApiTestCase;

class ArticleDetailTest extends ApiTestCase
{
    public function test_user_can_view_article_with_permission(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/articles/vue3-composition-api');

        $this->assertApiSuccess($response);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'slug',
                'description',
                'content',
                'category' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                ],
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertEquals('vue3-composition-api', $response->json('data.slug'));
    }

    public function test_user_cannot_view_article_without_permission(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/articles/laravel-best-practices');

        $this->assertApiError($response, 403, '0403');
    }

    public function test_non_existent_article_returns_404(): void
    {
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'gold@example.com',
            'password' => 'password',
        ]);

        $token = $loginResponse->json('data.access_token');

        $response = $this->withToken($token)->getJson('/api/articles/non-existent-slug');

        $this->assertApiError($response, 404, '0404');
    }

    public function test_unauthenticated_user_cannot_access_article(): void
    {
        $response = $this->getJson('/api/articles/vue3-composition-api');

        $this->assertApiError($response, 401, '0401');
    }
}
