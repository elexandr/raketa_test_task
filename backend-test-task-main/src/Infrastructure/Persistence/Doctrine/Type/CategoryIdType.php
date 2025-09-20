<?php

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Raketa\BackendTestTask\Domain\Category\CategoryId;

class CategoryIdType extends Type
{
    public const NAME = 'category_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 36;
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CategoryId
    {
        if ($value === null) {
            return null;
        }

        return CategoryId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof CategoryId) {
            return $value->toString();
        }

        if (is_string($value)) {
            return $value;
        }

        throw new \InvalidArgumentException('Expected CategoryId instance or string, got ' . gettype($value));
    }

    public function getName(): string
    {
        return self::NAME;
    }
}