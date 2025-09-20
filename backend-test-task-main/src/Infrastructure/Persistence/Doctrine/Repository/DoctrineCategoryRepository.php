<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Doctrine\Repository;

use Raketa\BackendTestTask\Domain\Category\Category;
use Raketa\BackendTestTask\Domain\Category\CategoryId;
use Raketa\BackendTestTask\Domain\Category\Repository\CategoryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Raketa\BackendTestTask\Application\Shared\Logger\LoggerInterface;

final class DoctrineCategoryRepository implements CategoryRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    ) {
        $this->repository = $entityManager->getRepository(Category::class);
    }

    public function findById(CategoryId $id): ?Category
    {
        try {
            return $this->repository->find($id);
        } catch (\Exception $e) {
            $this->logger->error('Database error when finding category by ID', [
                'categoryId' => $id->toString(),
                'exception' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
