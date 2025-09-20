<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\OpenAPI\Model;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AddItemRequest',
    title: 'Add Item Request',
    description: 'Request model for adding item to cart'
)]
class AddItemRequest
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')]
        public string $productId,

        #[OA\Property(type: 'integer', minimum: 1, example: 2)]
        public int $quantity
    ) {}
}