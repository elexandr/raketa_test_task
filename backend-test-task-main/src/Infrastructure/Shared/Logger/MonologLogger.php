<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Shared\Logger;

use Raketa\BackendTestTask\Application\Shared\Logger\LoggerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

final class MonologLogger implements LoggerInterface
{
    public function __construct(
        private readonly PsrLoggerInterface $logger
    ) {
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function __call(string $name, array $arguments)
    {
        return $this->logger->{$name}(...$arguments);
    }
}
