<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\Cart\Command;

use Raketa\BackendTestTask\Application\Shared\Exception\ApplicationException;
use Raketa\BackendTestTask\Application\Shared\Logger\LoggerInterface;
use Raketa\BackendTestTask\Domain\Cart\Cart;
use Raketa\BackendTestTask\Domain\Cart\CartItem;
use Raketa\BackendTestTask\Domain\Cart\Repository\CartRepositoryInterface;
use Raketa\BackendTestTask\Domain\Product\Exception\ProductNotFoundException;
use Raketa\BackendTestTask\Domain\Product\Repository\ProductRepositoryInterface;
use Raketa\BackendTestTask\Domain\Shared\Exception\DomainException;

final class AddItemToCartHandler
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws ApplicationException
     */
    public function __invoke(AddItemToCartCommand $command): void
    {
        try {
            $cart = $this->cartRepository->findById($command->cartId);
            
            if ($cart === null) {
                $cart = new Cart($command->cartId);
            }
            
            $product = $this->productRepository->findById($command->productId);

            if ($product === null) {
                $this->logger->warning('Product not found', ['productId' => $command->productId->toString()]);
                throw new ProductNotFoundException();
            }
            
            $cartItem = CartItem::create(
                $product->getId(),
                $product->getName(),
                $product->getPrice(),
                $command->quantity
            );
            $cart->addItem($cartItem);
            
            $this->cartRepository->save($cart);
            
        } catch (DomainException $e) {
            $this->logger->error('Domain exception in GetCartHandler', [
                'exception' => $e->getMessage(),
                'cartId' => $command->cartId->toString(),
            ]);
            throw new ApplicationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
