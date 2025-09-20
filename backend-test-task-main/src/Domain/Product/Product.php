<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Product;

use Raketa\BackendTestTask\Domain\Category\CategoryId;
use Raketa\BackendTestTask\Domain\Shared\ValueObject\Money;

readonly class Product
{
    private function __construct(
        private ProductId  $id,
        private string     $name,
        private Money      $price,
        private CategoryId $categoryId
    ) {
    }

    public function getId(): ProductId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getCategoryId(): CategoryId {
        return $this->categoryId;
    }

}