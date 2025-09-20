<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\Cart\Query;

use Raketa\BackendTestTask\Application\Shared\Exception\ApplicationException;
use Raketa\BackendTestTask\Application\Shared\Logger\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart\Cart;
use Raketa\BackendTestTask\Domain\Cart\Exception\CartNotFoundException;
use Raketa\BackendTestTask\Domain\Cart\Repository\CartRepositoryInterface;
use Raketa\BackendTestTask\Domain\Shared\Exception\DomainException;

final class GetCartHandler
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws ApplicationException
     */
    public function __invoke(GetCartQuery $query): Cart
    {
        try {
            $cart = $this->cartRepository->findById($query->cartId);

            if ($cart === null) {
                $this->logger->warning('Cart not found', ['cartId' => $query->cartId->toString()]);
                throw new CartNotFoundException();
            }
            
            return $cart;
        } catch (DomainException $e) {
            $this->logger->error('Domain exception in GetCartHandler', [
                'exception' => $e->getMessage(),
                'cartId' => $query->cartId->toString()
            ]);
            throw new ApplicationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
