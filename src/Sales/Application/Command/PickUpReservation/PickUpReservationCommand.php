<?php

declare(strict_types=1);

namespace App\Sales\Application\Command\PickUpReservation;

class PickUpReservationCommand
{
    public function __construct(
        public readonly string $reservationId
    ) {
    }
}
