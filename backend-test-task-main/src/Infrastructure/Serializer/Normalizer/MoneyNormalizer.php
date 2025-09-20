<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Serializer\Normalizer;

use Raketa\BackendTestTask\Domain\Shared\ValueObject\Money;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class MoneyNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize($data, string $format = null, array $context = []): array
    {
        return $data->jsonSerialize();
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Money;
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): Money
    {
        return new Money($data['amount']);
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Money::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Money::class => true];
    }
}