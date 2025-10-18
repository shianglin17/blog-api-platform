<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected function assertApiError(TestResponse $response, int $httpStatus, string $code): void
    {
        $assertions = ['success' => false, 'code' => $code];

        if ($httpStatus !== 422) {
            $messages = [
                401 => 'Unauthenticated',
                403 => 'Forbidden',
                500 => 'Internal Server Error',
            ];
            if (isset($messages[$httpStatus])) {
                $assertions['message'] = $messages[$httpStatus];
            }
        }

        $response->assertStatus($httpStatus)
            ->assertJsonStructure(['message'])
            ->assertJson($assertions);
    }

    protected function assertApiSuccess(TestResponse $response, string $code = '0200'): void
    {
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'message'])
            ->assertJson(['success' => true, 'code' => $code]);
    }
}
