<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Doctrine\Repository;

use Raketa\BackendTestTask\Domain\Product\Product;
use Raketa\BackendTestTask\Domain\Product\ProductId;
use Raketa\BackendTestTask\Domain\Product\Repository\ProductRepositoryInterface;
use Raketa\BackendTestTask\Domain\Category\CategoryId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Raketa\BackendTestTask\Application\Shared\Logger\LoggerInterface;

final class DoctrineProductRepository implements ProductRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
        $this->repository = $entityManager->getRepository(Product::class);
    }

    public function findById(ProductId $id): ?Product
    {
        try {
            $product = $this->repository->find($id->toString());

            if ($product === null) {
                $this->logger->warning('Product not found in database', ['productId' => $id->toString()]);
            }
            
            return $product;
        } catch (\Exception $e) {
            $this->logger->error('Database error when finding product by ID', [
                'productId' => $id->toString(),
                'exception' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function findByCategoryId(CategoryId $categoryId): array
    {

        try {
            $products = $this->repository->createQueryBuilder('p')
                ->where('p.categoryId = :categoryId')
                ->setParameter('categoryId', $categoryId->toString())
                ->getQuery()
                ->getResult();

            return $products;
        } catch (\Exception $e) {
            $this->logger->error('Database error when finding products by category', [
                'categoryId' => $categoryId->toString(),
                'exception' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
