<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Serializer\Normalizer;

use Raketa\BackendTestTask\Domain\Shared\ValueObject\Quantity;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class QuantityNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize($data, string $format = null, array $context = []): array
    {
        return $data->jsonSerialize();
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Quantity;
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): Quantity
    {
        return new Quantity($data['value']);
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Quantity::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Quantity::class => true];
    }
}