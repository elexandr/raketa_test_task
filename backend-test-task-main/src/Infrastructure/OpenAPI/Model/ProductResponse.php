<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\OpenAPI\Model;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductResponse',
    title: 'Product Response',
    description: 'Product response model'
)]
class ProductResponse
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')]
        public string $id,

        #[OA\Property(type: 'string', example: 'Product Name')]
        public string $name,

        #[OA\Property(type: 'integer', description: 'Price in cents', example: 1000)]
        public int $price,

        #[OA\Property(type: 'string', format: 'uuid', example: 'f47ac10b-58cc-4372-a567-0e02b2c3d479')]
        public string $categoryId
    ) {}
}