<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Redis;

use JetBrains\PhpStorm\ArrayShape;

final readonly class CartData
{
    public function __construct(
        public string $id,
        #[ArrayShape([
            'productId' => 'string',
            'productName' => 'string',
            'price' => ['amount' => 'int'],
            'quantity' => ['value' => 'int']
        ])]
        public array  $items
    ) {
    }
}