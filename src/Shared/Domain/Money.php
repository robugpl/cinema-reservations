<?php

declare(strict_types=1);

namespace App\Shared\Domain;

class Money
{
    public function __construct(private readonly int $amountInMinorUnit)
    {
    }

    public function __toString(): string
    {
        return (string) $this->amountInMinorUnit;
    }

    public function toString(): string
    {
        return (string) $this->amountInMinorUnit;
    }

    public function getAmountInMinorUnit(): int
    {
        return $this->amountInMinorUnit;
    }
}
