<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Shared\ValueObject;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        private int $value // в копейках
    )
    {
        if ($this->value < 0) {
            throw new InvalidArgumentException('Money cannot be negative');
        }
    }

    public function add(self $other): self
    {
         return new self($this->value + $other->value);
    }

    public function subtract(self $other): self
    {
         return new self($this->value - $other->value);
    }

    public function multiply(Quantity $multiplier): self
    {
        return new self($this->value * $multiplier->getValue());
    }

    public function getAmount(): int
    {
        return $this->value;
    }

    public function jsonSerialize(): array
    {
        return ['amount' => $this->value];
    }
}