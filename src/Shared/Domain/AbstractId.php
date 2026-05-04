<?php

declare(strict_types=1);

namespace App\Shared\Domain;

abstract class AbstractId
{
    public function __construct(protected readonly string $value)
    {
    }

    protected static function getPrefix(): string
    {
        return '';
    }

    public static function generate(): static
    {
        return new static(static::getPrefix() . uniqid('', true));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
