<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Shared\Exception;

class DomainException extends \DomainException
{
    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
