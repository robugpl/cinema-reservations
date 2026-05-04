<?php

declare(strict_types=1);

namespace App\Reservations\Domain\ScreeningRoom;

class Row
{
    /**
     * @param Seat[] $seats
     */
    public function __construct(
        private readonly int $rowNumber,
        private array $seats = []
    ) {
    }

    public function getRowNumber(): int
    {
        return $this->rowNumber;
    }

    /**
     * @return Seat[]
     */
    public function getSeats(): array
    {
        return $this->seats;
    }
}
