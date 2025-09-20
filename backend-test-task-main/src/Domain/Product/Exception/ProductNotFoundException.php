<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Product\Exception;

use Raketa\BackendTestTask\Domain\Shared\Exception\DomainException;

final class ProductNotFoundException extends DomainException
{
    public function __construct(string $message = 'Product not found')
    {
        parent::__construct($message);
    }
}
