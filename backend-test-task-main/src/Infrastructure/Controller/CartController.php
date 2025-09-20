<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Controller;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Raketa\BackendTestTask\Application\Cart\Query\GetCartQuery;
use Raketa\BackendTestTask\Application\Cart\Query\GetCartHandler;
use Raketa\BackendTestTask\Application\Cart\Command\AddItemToCartCommand;
use Raketa\BackendTestTask\Application\Cart\Command\AddItemToCartHandler;
use Raketa\BackendTestTask\Application\Shared\Exception\ApplicationException;
use Raketa\BackendTestTask\Domain\Cart\CartId;
use Raketa\BackendTestTask\Domain\Cart\CartItem;
use Raketa\BackendTestTask\Domain\Product\ProductId;
use Raketa\BackendTestTask\Domain\Shared\ValueObject\Quantity;
use Raketa\BackendTestTask\Infrastructure\OpenAPI\Model\AddItemRequest;
use Raketa\BackendTestTask\Infrastructure\OpenAPI\Model\CartResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Raketa\BackendTestTask\Application\Shared\Logger\LoggerInterface;

final class CartController
{
    public function __construct(
        private readonly GetCartHandler $getCartHandler,
        private readonly AddItemToCartHandler $addItemToCartHandler,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('/api/carts/{cartId}', name: 'api_cart_get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get cart contents',
        description: 'Returns the contents of a shopping cart',
        tags: ['Cart']
    )]
    #[OA\Parameter(
        name: 'cartId',
        in: 'path',
        description: 'UUID of the cart',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful operation',
        content: new OA\JsonContent(ref: new Model(type: CartResponse::class))
    )]
    public function getCart(string $cartId): JsonResponse
    {
        try {
            $cartId = CartId::fromString($cartId);
            $query = new GetCartQuery($cartId);
            $cart = ($this->getCartHandler)($query);

            return new JsonResponse([
                'id' => $cart->getId()->toString(),
                'items' => array_map(fn(CartItem $item) => [
                    'productId' => $item->getProductId()->toString(),
                    'productName' => $item->getProductName(),
                    'quantity' => $item->getQuantity()->getValue(),
                    'price' => $item->getPrice()->getAmount(),
                    'subtotal' => $item->calculateSubtotal()->getAmount()
                ], $cart->getItems()),
                'totalQuantity' => $cart->getTotalQuantity()->getValue(),
                'totalAmount' => $cart->calculateTotal()->getAmount()
            ]);

        } catch (ApplicationException $e) {
            $this->logger->error('Application error in getCart', [
                'exception' => $e->getMessage(),
                'cartId' => $cartId,
                'code' => $e->getCode()
            ]);
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('Invalid argument in getCart', [
                'exception' => $e->getMessage(),
                'cartId' => $cartId
            ]);
            return new JsonResponse(['error' => 'Invalid cart ID format'], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error in getCart', [
                'exception' => $e->getMessage(),
                'cartId' => $cartId,
                'trace' => $e->getTraceAsString()
            ]);
            return new JsonResponse(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/carts/{cartId}/items', name: 'api_cart_add_item', methods: ['POST'])]
    #[OA\Post(
        summary: 'Add item to cart',
        description: 'Adds a product item to the shopping cart',
        tags: ['Cart']
    )]
    #[OA\Parameter(
        name: 'cartId',
        in: 'path',
        description: 'UUID of the cart',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid')
    )]
    #[OA\RequestBody(
        description: 'Item to add to cart',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: AddItemRequest::class))
    )]
    #[OA\Response(
        response: 201,
        description: 'Item successfully added'
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid input'
    )]
    public function addItemToCart(string $cartId, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            if (!isset($data['productId'], $data['quantity'])) {
                $this->logger->warning('Missing required fields in addItemToCart', [
                    'cartId' => $cartId,
                    'data' => $data
                ]);
                return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }

            $cartId = CartId::fromString($cartId);
            $productId = ProductId::fromString($data['productId']);
            $quantity = new Quantity((int)$data['quantity']);

            $command = new AddItemToCartCommand($cartId, $productId, $quantity);
            ($this->addItemToCartHandler)($command);

            return new JsonResponse(['status' => 'Item added to cart'], Response::HTTP_CREATED);

        } catch (ApplicationException $e) {
            $this->logger->error('Application error in addItemToCart', [
                'exception' => $e->getMessage(),
                'cartId' => $cartId,
                'code' => $e->getCode()
            ]);
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\JsonException $e) {
            $this->logger->warning('JSON parsing error in addItemToCart', [
                'exception' => $e->getMessage(),
                'cartId' => $cartId
            ]);
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('Invalid argument in addItemToCart', [
                'exception' => $e->getMessage(),
                'cartId' => $cartId
            ]);
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error in addItemToCart', [
                'exception' => $e->getMessage(),
                'cartId' => $cartId,
                'trace' => $e->getTraceAsString()
            ]);
            return new JsonResponse(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
