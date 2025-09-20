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

    public function isConnected(): bool
    {
        try {
            $connected = $this->client !== null && $this->client->ping() === true;
            if (!$connected) {
                $this->logger->warning('Redis connection check failed');
            }
            return $connected;
        } catch (\RedisException $e) {
            $this->logger->error('Redis ping failed', ['exception' => $e->getMessage()]);
            return false;
        }
    }

    public function testConnection(): bool
    {
        try {
            $client = $this->getClient();
            $result = $client->ping() === true;
            
            if ($result) {
                $this->logger->debug('Redis connection test successful');
            }
            
            return $result;
        } catch (\RedisException $e) {
            $this->logger->error('Redis connection test failed', ['exception' => $e->getMessage()]);
            return false;
        } catch (RedisConnectionException $e) {
            $this->logger->error('Redis connection test failed', ['exception' => $e->getMessage()]);
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
                $this->logger->emergency('Failed to connect to Redis - connection returned false');
                throw new RedisConnectionException('Failed to connect to Redis');
            }

            if ($this->auth !== null) {
                $authenticated = $this->client->auth($this->auth);
                if (!$authenticated) {
                    $this->logger->emergency('Redis authentication failed');
                    throw new RedisConnectionException('Redis authentication failed');
                }
            }

            // Test the connection
            if ($this->client->ping() !== true) {
                $this->logger->emergency('Redis ping failed after connection');
                throw new RedisConnectionException('Redis ping failed');
            }

            $this->logger->info('Redis connection established successfully');

        } catch (\RedisException $e) {
            $this->logger->emergency('Redis connection error', [
                'exception' => $e->getMessage(),
                'host' => $this->host,
                'port' => $this->port
            ]);
            throw new RedisConnectionException('Redis connection error: ' . $e->getMessage(), 0, $e);
        }
    }
}
