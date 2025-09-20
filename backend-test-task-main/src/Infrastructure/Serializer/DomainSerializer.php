<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure\Serializer;

use Raketa\BackendTestTask\Infrastructure\Serializer\Normalizer\BaseIdNormalizer;
use Raketa\BackendTestTask\Infrastructure\Serializer\Normalizer\MoneyNormalizer;
use Raketa\BackendTestTask\Infrastructure\Serializer\Normalizer\QuantityNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class DomainSerializer implements SerializerInterface
{
    private Serializer $serializer;

    public function __construct(
        ObjectNormalizer $objectNormalizer,
        MoneyNormalizer $moneyNormalizer,
        QuantityNormalizer $quantityNormalizer,
        BaseIdNormalizer $baseIdNormalizer,
        JsonEncoder $jsonEncoder
    ) {
        $normalizers = [
            $baseIdNormalizer,
            $moneyNormalizer,
            $quantityNormalizer,
            $objectNormalizer
        ];
        $encoders = [$jsonEncoder];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function serialize($data, string $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }
}