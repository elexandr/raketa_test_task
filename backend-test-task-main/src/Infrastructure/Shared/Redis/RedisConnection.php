<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Shared\Redis;

use Raketa\BackendTestTask\Infrastructure\Shared\Redis\Exception\RedisConnectionException;
use Raketa\BackendTestTask\Application\Shared\Logger\LoggerInterface;

final class RedisConnection implements RedisConnectionInterface
{
    private ?\Redis $client = null;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $host,
        private readonly int $port,
        private readonly float $timeout,
        private readonly ?string $auth = null,
    ) {
    }

    public function getClient(): \Redis
    {
        if ($this->client === null) {
            $this->connect();
        }

        return $this->client;
    }

    public function isAvailable(): bool
    {
        try {
            return $this->getClient()->ping() === true;
        } catch (\RedisException | RedisConnectionException $e) {
            $this->logger->warning('Redis availability check failed', ['exception' => $e->getMessage()]);
            return false;
        }
    }

    public function isWritable(): bool
    {
        try {
            $client = $this->getClient();
            $testKey = 'write_test:' . uniqid('', true);
            $testValue = 'test_value';
            $ttl = 2;

            $result = $client->setex($testKey, $ttl, $testValue);
            $value = $client->get($testKey);
            $client->del($testKey);

            return $result && $value === $testValue;
        } catch (\RedisException | RedisConnectionException $e) {
            $this->logger->warning('Redis write check failed', ['exception' => $e->getMessage()]);
            return false;
        }
    }

    private function connect(): void
    {
        try {
            $this->logger->info('Connecting to Redis', [
                'host' => $this->host,
                'port' => $this->port,
                'timeout' => $this->timeout
            ]);

            $this->client = new \Redis();
            $connected = $this->client->connect($this->host, $this->port, $this->timeout);

            if (!$connected) {
                throw new RedisConnectionException('Failed to connect to Redis');
            }

            if ($this->auth !== null && !$this->client->auth($this->auth)) {
                throw new RedisConnectionException('Redis authentication failed');
            }

            $this->logger->info('Redis connection established successfully');

        } catch (\RedisException $e) {
            $this->logger->error('Redis connection failed', [
                'exception' => $e->getMessage(),
                'host' => $this->host,
                'port' => $this->port
            ]);
            throw new RedisConnectionException('Redis connection failed: ' . $e->getMessage(), 0, $e);
        }
    }
}