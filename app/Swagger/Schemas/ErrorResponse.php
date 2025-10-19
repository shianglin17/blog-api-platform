<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ErrorResponse',
    properties: [
        new OA\Property(property: 'data', type: 'null'),
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'code', type: 'string', example: '0401'),
        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
    ],
    type: 'object'
)]
class ErrorResponse
{
}
