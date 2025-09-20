<?php

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Raketa\BackendTestTask\Domain\Product\ProductId;

class ProductIdType extends Type
{
    public const NAME = 'product_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 36;
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProductId
    {
        if ($value === null) {
            return null;
        }

        return ProductId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ProductId) {
            return $value->toString();
        }

        if (is_string($value)) {
            return $value;
        }

        throw new \InvalidArgumentException('Expected ProductId instance or string, got ' . gettype($value));
    }

    public function getName(): string
    {
        return self::NAME;
    }
}