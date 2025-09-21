<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Shared\Redis;

interface RedisConnectionInterface
{
    public function getClient(): \Redis;
    public function isAvailable(): bool;
    public function isWritable(): bool;
}