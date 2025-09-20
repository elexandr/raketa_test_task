<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Cart\Repository;

use Raketa\BackendTestTask\Domain\Cart\Cart;
use Raketa\BackendTestTask\Domain\Cart\CartId;

interface CartRepositoryInterface
{
    public function findById(CartId $id): ?Cart;
    public function save(Cart $cart): void;
}
