<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Serializer\Normalizer;

use Raketa\BackendTestTask\Domain\Shared\Identity\BaseId;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class BaseIdNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize($data, string $format = null, array $context = []): string
    {
        return $data->toString();
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof BaseId;
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): BaseId
    {
        return $type::fromString($data);
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return is_subclass_of($type, BaseId::class);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [BaseId::class => true];
    }
}