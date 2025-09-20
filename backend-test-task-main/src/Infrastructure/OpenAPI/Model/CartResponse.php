<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\OpenAPI\Model;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CartResponse',
    title: 'Cart Response',
    description: 'Shopping cart response model'
)]
class CartResponse
{
    public function __construct(
        #[OA\Property(type: 'string', format: 'uuid', example: 'f47ac10b-58cc-4372-a567-0e02b2c3d479')]
        public string $id,

        #[OA\Property(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'productId', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000'),
                    new OA\Property(property: 'productName', type: 'string', example: 'Product Name'),
                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
                    new OA\Property(property: 'price', type: 'integer', example: 1000),
                    new OA\Property(property: 'subtotal', type: 'integer', example: 2000),
                ]
            )
        )]
        public array $items,

        #[OA\Property(type: 'integer', example: 5)]
        public int $totalQuantity,

        #[OA\Property(type: 'integer', example: 5000)]
        public int $totalAmount
    ) {}
}