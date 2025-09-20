<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Category\Exception;

use Raketa\BackendTestTask\Domain\Shared\Exception\DomainException;

final class CategoryNotFoundException extends DomainException
{
    public function __construct(string $message = 'Category not found')
    {
        parent::__construct($message);
    }
}
