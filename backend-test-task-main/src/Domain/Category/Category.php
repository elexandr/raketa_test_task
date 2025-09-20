<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Domain\Category;

readonly class Category
{
    private function __construct(
        private CategoryId $id,
        private string     $name,
    ) {
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
