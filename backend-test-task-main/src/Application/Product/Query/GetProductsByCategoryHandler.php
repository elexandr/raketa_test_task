<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Application\Product\Query;

use Raketa\BackendTestTask\Application\Shared\Exception\ApplicationException;
use Raketa\BackendTestTask\Application\Shared\Logger\LoggerInterface;
use Raketa\BackendTestTask\Domain\Category\Exception\CategoryNotFoundException;
use Raketa\BackendTestTask\Domain\Category\Repository\CategoryRepositoryInterface;
use Raketa\BackendTestTask\Domain\Product\Product;
use Raketa\BackendTestTask\Domain\Product\Repository\ProductRepositoryInterface;
use Raketa\BackendTestTask\Domain\Shared\Exception\DomainException;

final class GetProductsByCategoryHandler
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return Product[]
     * @throws ApplicationException
     */
    public function __invoke(GetProductsByCategoryQuery $query): array
    {
        try {
            $category = $this->categoryRepository->findById($query->categoryId);

            if ($category === null) {
                $this->logger->error('Category not found',['category_id' => $query->categoryId,]);
                throw new CategoryNotFoundException();
            }
            
            return $this->productRepository->findByCategoryId($query->categoryId);
            
        } catch (DomainException $e) {
            $this->logger->error('Error while fetching products by category', [
                'exception' => $e->getMessage(),
                'category_id' => $query->categoryId,
            ]);
            throw new ApplicationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
