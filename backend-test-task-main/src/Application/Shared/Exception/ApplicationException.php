<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\Shared\Exception;

class ApplicationException extends \RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
