<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\Cart\Query;

use Raketa\BackendTestTask\Domain\Cart\CartId;

final readonly class GetCartQuery
{
    public function __construct(
        public CartId $cartId
    ) {
    }
}
