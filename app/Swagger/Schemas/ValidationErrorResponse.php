<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ValidationErrorResponse',
    properties: [
        new OA\Property(property: 'data', type: 'null'),
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'code', type: 'string', example: '0422'),
        new OA\Property(property: 'message', type: 'string', example: 'Validation failed'),
        new OA\Property(
            property: 'errors',
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'array',
                    items: new OA\Items(type: 'string', example: 'The email field is required.')
                ),
                new OA\Property(
                    property: 'password',
                    type: 'array',
                    items: new OA\Items(type: 'string', example: 'The password field is required.')
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class ValidationErrorResponse
{
}
