<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Product\Repository;

use Raketa\BackendTestTask\Domain\Product\Product;
use Raketa\BackendTestTask\Domain\Product\ProductId;
use Raketa\BackendTestTask\Domain\Category\CategoryId;

interface ProductRepositoryInterface
{
    public function findById(ProductId $id): ?Product;

    /**
     * @param CategoryId $categoryId
     * @return Product[]
     */
    public function findByCategoryId(CategoryId $categoryId): array;
}
