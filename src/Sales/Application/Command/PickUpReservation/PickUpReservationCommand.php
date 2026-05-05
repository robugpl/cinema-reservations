<?php

declare(strict_types=1);

namespace App\Sales\Application\Command\PickUpReservation;

class PickUpReservationCommand
{
    /**
     * @param array<int, array{screeningId: string, seatId: string, priceInMinorUnits: int}> $tickets
     */
    public function __construct(
        public readonly string $email,
        public readonly array $tickets
    ) {
    }
}
