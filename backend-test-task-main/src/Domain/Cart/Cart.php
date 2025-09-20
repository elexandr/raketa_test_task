<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Cart;

use Raketa\BackendTestTask\Domain\Product\ProductId;
use Raketa\BackendTestTask\Domain\Shared\ValueObject\Money;
use Raketa\BackendTestTask\Domain\Shared\ValueObject\Quantity;

class Cart
{
    /** @var CartItem[] */
    private array $items = [];

    public function __construct(
        private readonly CartId $id
    )
    {
    }

    public function getId(): CartId
    {
        return $this->id;
    }
    public function addItem(CartItem $newItem): void
    {
        foreach ($this->items as $index => $item) {
            if ($item->hasSameProduct($newItem)) {
                $this->items[$index] = $item->withIncreasedQuantity($newItem->getQuantity());
                return;
            }
        }

        $this->items[] = $newItem;
    }

    public function getItems(): array
    {
        return array_map(fn(CartItem $item) => clone $item, $this->items);
    }

    public function getItemByProductId(ProductId $productId): ?CartItem
    {
        foreach ($this->items as $item) {
            if ($item->getProductId()->equals($productId)) {
                return clone $item;
            }
        }

        return null;
    }

    public function getTotalQuantity(): Quantity
    {
        $total = new Quantity(0);
        foreach ($this->items as $item) {
            $total = $total->add($item->getQuantity());
        }
        return $total;
    }

    public function calculateTotal(): Money
    {
        $total = new Money(0);

        foreach ($this->items as $item) {
            $total = $total->add($item->calculateSubtotal());
        }

        return $total;
    }
}