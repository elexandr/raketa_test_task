<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Shared\Redis;

interface RedisConnectionInterface
{
    public function getClient(): \Redis;
    public function isConnected(): bool;
    public function testConnection(): bool;
}
