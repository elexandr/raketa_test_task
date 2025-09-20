<?php

namespace Raketa\BackendTestTask\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Raketa\BackendTestTask\Domain\Shared\ValueObject\Quantity;

class QuantityType extends Type
{
    public const NAME = 'quantity';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Quantity
    {
        if ($value === null) {
            return null;
        }

        return new Quantity((int)$value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Quantity) {
            throw new \InvalidArgumentException('Expected Quantity instance');
        }

        return $value->getValue();
    }

    public function getName(): string
    {
        return self::NAME;
    }
}