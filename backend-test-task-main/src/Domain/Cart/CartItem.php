<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Cart;

use Raketa\BackendTestTask\Domain\Product\ProductId;
use Raketa\BackendTestTask\Domain\Shared\ValueObject\Money;
use Raketa\BackendTestTask\Domain\Shared\ValueObject\Quantity;

final readonly class CartItem
{
    private function __construct(
        private ProductId $productId,
        private string    $productName,
        private Money     $price,
        private Quantity  $quantity
    ) {
    }

    public static function create(
        ProductId $productId,
        string $productName,
        Money $price,
        Quantity $quantity
    ): self
    {
        return new self(
            $productId,
            $productName,
            $price,
            $quantity
        );
    }

    public function withIncreasedQuantity(Quantity $value): self
    {
        return new self($this->productId, $this->productName, $this->price, $this->quantity->add($value));
    }

    public function calculateSubtotal(): Money
    {
        return $this->price->multiply($this->quantity);
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    private function equals(self $other): bool
    {
        return $this->productId->equals($other->productId);
    }

    public function hasSameProduct(self $other): bool
    {
        return $this->equals($other);
    }
}