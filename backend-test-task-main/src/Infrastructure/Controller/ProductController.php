<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Controller;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Raketa\BackendTestTask\Application\Product\Query\GetProductsByCategoryQuery;
use Raketa\BackendTestTask\Application\Product\Query\GetProductsByCategoryHandler;
use Raketa\BackendTestTask\Application\Shared\Exception\ApplicationException;
use Raketa\BackendTestTask\Domain\Category\CategoryId;
use Raketa\BackendTestTask\Infrastructure\OpenAPI\Model\ProductResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Raketa\BackendTestTask\Application\Shared\Logger\LoggerInterface;

final class ProductController
{
    public function __construct(
        private readonly GetProductsByCategoryHandler $getProductsByCategoryHandler,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/api/categories/{categoryId}/products', name: 'api_products_by_category', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get products by category',
        description: 'Returns a list of products for the specified category',
        tags: ['Products']
    )]
    #[OA\Parameter(
        name: 'categoryId',
        in: 'path',
        description: 'UUID of the category',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful operation',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ProductResponse::class))
        )
    )]
    #[OA\Response(
        response: 404,
        description: 'Category not found'
    )]
    public function getProductsByCategory(string $categoryId): JsonResponse
    {
        try {
            $categoryId = CategoryId::fromString($categoryId);
            $query = new GetProductsByCategoryQuery($categoryId);
            $products = ($this->getProductsByCategoryHandler)($query);

            return new JsonResponse(array_map(fn($product) => [
                'id' => $product->getId()->toString(),
                'name' => $product->getName(),
                'price' => $product->getPrice()->getAmount(),
                'categoryId' => $product->getCategoryId()->toString()
            ], $products));

        } catch (ApplicationException $e) {
            $this->logger->error('Application error in getProductsByCategory', [
                'exception' => $e->getMessage(),
                'categoryId' => $categoryId,
                'code' => $e->getCode()
            ]);
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('Invalid argument in getProductsByCategory', [
                'exception' => $e->getMessage(),
                'categoryId' => $categoryId
            ]);
            return new JsonResponse(['error' => 'Invalid category ID format'], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error in getProductsByCategory', [
                'exception' => $e->getMessage(),
                'categoryId' => $categoryId,
                'trace' => $e->getTraceAsString()
            ]);
            return new JsonResponse(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
