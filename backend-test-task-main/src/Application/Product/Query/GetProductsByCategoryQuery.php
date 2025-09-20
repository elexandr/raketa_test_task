<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\Product\Query;

use Raketa\BackendTestTask\Domain\Category\CategoryId;

final readonly class GetProductsByCategoryQuery
{
    public function __construct(
        public CategoryId $categoryId
    ) {
    }
}
