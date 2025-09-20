<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Shared\Identity;

interface IdInterface
{
    public static function fromString(string $value): IdInterface;
    public function toString(): string;
    public function equals(IdInterface $other): bool;
}