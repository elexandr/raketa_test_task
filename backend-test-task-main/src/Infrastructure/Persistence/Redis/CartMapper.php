<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Redis;

use Raketa\BackendTestTask\Domain\Cart\Cart;
use Raketa\BackendTestTask\Domain\Cart\CartId;
use Raketa\BackendTestTask\Domain\Cart\CartItem;
use Raketa\BackendTestTask\Domain\Product\ProductId;
use Raketa\BackendTestTask\Domain\Shared\ValueObject\Money;
use Raketa\BackendTestTask\Domain\Shared\ValueObject\Quantity;

final class CartMapper
{
    public function toData(Cart $cart): CartData
    {
        $items = [];
        foreach ($cart->getItems() as $item) {
            $items[] = [
                'productId' => $item->getProductId()->toString(),
                'productName' => $item->getProductName(),
                'price' => ['amount' => $item->getPrice()->getAmount()],
                'quantity' => ['value' => $item->getQuantity()->getValue()],
            ];
        }

        return new CartData($cart->getId()->toString(), $items);
    }

    public function toDomain(CartData $data): Cart
    {
        $cart = new Cart(CartId::fromString($data->id));

        foreach ($data->items as $itemData) {
            $cartItem = CartItem::create(
                ProductId::fromString($itemData['productId']),
                $itemData['productName'],
                new Money($itemData['price']['amount']),
                new Quantity($itemData['quantity']['value'])
            );

            $cart->addItem($cartItem);
        }

        return $cart;
    }
}