<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Shared\ValueObject;

use InvalidArgumentException;

final readonly class Quantity
{
    public function __construct(
        private int $value
    ) {
        if ($this->value < 0) {
            throw new InvalidArgumentException('Quantity cannot be negative');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function add(self $amount): self
    {
        $result = $this->value + $amount->getValue();
        return new self($result);
    }

    public function subtract(self $amount): self
    {
        $result = $this->value - $amount->value;
        return new self($result);
    }

    public function jsonSerialize(): array
    {
        return ['value' => $this->value];
    }
}
