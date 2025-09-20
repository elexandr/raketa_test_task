<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Shared\Redis\Exception;

class RedisConnectionException extends \RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
