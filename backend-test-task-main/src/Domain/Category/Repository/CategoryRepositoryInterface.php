<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Category\Repository;

use Raketa\BackendTestTask\Domain\Category\Category;
use Raketa\BackendTestTask\Domain\Category\CategoryId;

interface CategoryRepositoryInterface
{
    public function findById(CategoryId $id): ?Category;
}
