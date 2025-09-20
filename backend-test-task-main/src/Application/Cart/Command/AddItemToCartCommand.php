<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\Cart\Command;

use Raketa\BackendTestTask\Domain\Cart\CartId;
use Raketa\BackendTestTask\Domain\Product\ProductId;
use Raketa\BackendTestTask\Domain\Shared\ValueObject\Quantity;

final readonly class AddItemToCartCommand
{
    public function __construct(
        public CartId $cartId,
        public ProductId $productId,
        public Quantity $quantity
    ) {
    }
}
