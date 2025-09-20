<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\Shared\Logger;

interface LoggerInterface
{
    public function error(string $message, array $context = []): void;
    public function warning(string $message, array $context = []): void;
}