<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Redis;

use Raketa\BackendTestTask\Domain\Cart\Cart;
use Raketa\BackendTestTask\Domain\Cart\CartId;
use Raketa\BackendTestTask\Domain\Cart\Repository\CartRepositoryInterface;
use Raketa\BackendTestTask\Infrastructure\Shared\Redis\RedisConnectionInterface;
use Raketa\BackendTestTask\Infrastructure\Shared\Redis\Exception\RedisConnectionException;
use Raketa\BackendTestTask\Application\Shared\Logger\LoggerInterface;
use RedisException;

final class RedisCartRepository implements CartRepositoryInterface
{
    private const CART_KEY_PREFIX = 'cart:';
    private const TTL_SECONDS = 86400;

    public function __construct(
        private readonly RedisConnectionInterface $redisConnection,
        private readonly LoggerInterface $logger,
        private readonly CartMapper $cartMapper
    ) {
    }

    public function findById(CartId $id): ?Cart
    {
        try {
            $redis = $this->redisConnection->getClient();
            $key = $this->getKey($id);

            $data = $redis->get($key);

            if ($data === false) {
                $this->logger->info('Cart not found in Redis', ['key' => $key]);
                return null;
            }

            $cartDataArray = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
            $cartData = new CartData($cartDataArray['id'], $cartDataArray['items']);

            return $this->cartMapper->toDomain($cartData);
        } catch (RedisException $e) {
            $this->logger->error('Failed to get cart from Redis', [
                'key' => $this->getKey($id),
                'exception' => $e->getMessage()
            ]);
            throw new RedisConnectionException('Failed to get cart from Redis: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            $this->logger->error('Failed to deserialize cart from Redis', [
                'key' => $this->getKey($id),
                'exception' => $e->getMessage()
            ]);
            throw new RedisConnectionException('Failed to deserialize cart: ' . $e->getMessage(), 0, $e);
        }
    }

    public function save(Cart $cart): void
    {
        try {
            $redis = $this->redisConnection->getClient();
            $key = $this->getKey($cart->getId());

            $cartData = $this->cartMapper->toData($cart);
            $data = json_encode([
                'id' => $cartData->id,
                'items' => $cartData->items
            ], JSON_THROW_ON_ERROR);

            $result = $redis->setex($key, self::TTL_SECONDS, $data);

            if ($result === false) {
                $this->logger->error('Failed to save cart to Redis - setex returned false', ['key' => $key]);
                throw new RedisConnectionException('Failed to save cart to Redis');
            }

            $this->logger->info('Cart successfully saved to Redis', [
                'key' => $key,
                'items_count' => count($cart->getItems())
            ]);

        } catch (RedisException $e) {
            $this->logger->error('Failed to save cart to Redis', [
                'key' => $this->getKey($cart->getId()),
                'exception' => $e->getMessage()
            ]);
            throw new RedisConnectionException('Failed to save cart to Redis: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            $this->logger->error('Failed to serialize cart for Redis', [
                'key' => $this->getKey($cart->getId()),
                'exception' => $e->getMessage()
            ]);
            throw new RedisConnectionException('Failed to serialize cart: ' . $e->getMessage(), 0, $e);
        }
    }

    private function getKey(CartId $id): string
    {
        return self::CART_KEY_PREFIX . $id->toString();
    }
}