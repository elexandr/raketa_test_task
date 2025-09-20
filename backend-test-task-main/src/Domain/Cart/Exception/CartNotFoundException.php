<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Cart\Exception;

use Raketa\BackendTestTask\Domain\Shared\Exception\DomainException;

final class CartNotFoundException extends DomainException
{
    public function __construct(string $message = 'Cart not found')
    {
        parent::__construct($message);
    }
}
