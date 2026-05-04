<?php

declare(strict_types=1);

namespace App\Reservations\Domain\ScreeningRoom;

class Seat
{
    public function __construct(
        private readonly SeatId $id,
        private readonly string $label
    ) {
    }

    public function getId(): SeatId
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
