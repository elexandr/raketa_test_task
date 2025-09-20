<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Shared\Identity;

abstract class BaseId implements IdInterface
{
    public function __construct(
        private readonly string $value
    )
    {
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(IdInterface $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

}
